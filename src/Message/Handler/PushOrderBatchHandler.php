<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Persistence\ManagerRegistry;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Exception\ClientException;
use Setono\SyliusMailchimpPlugin\Message\Command\PushOrderBatch;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;
use Throwable;

final class PushOrderBatchHandler implements MessageHandlerInterface
{
    /** @var Workflow|null */
    private $workflow;

    /** @var QueryRebuilderInterface */
    private $queryRebuilder;

    /** @var ClientInterface */
    private $client;

    /** @var ManagerRegistry */
    private $managerRegistry;

    /** @var Registry */
    private $workflowRegistry;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        ClientInterface $client,
        ManagerRegistry $managerRegistry,
        Registry $workflowRegistry
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->client = $client;
        $this->managerRegistry = $managerRegistry;
        $this->workflowRegistry = $workflowRegistry;
    }

    public function __invoke(PushOrderBatch $message): void
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());
        $manager = $this->managerRegistry->getManagerForClass($message->getBatch()->getClass());
        if (null === $manager) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'No object manager available for class %s', $message->getBatch()->getClass()
            ));
        }

        /** @var OrderInterface[] $orders */
        $orders = $q->getResult();

        foreach ($orders as $order) {
            $workflow = $this->getWorkflow($order);

            // todo use constant
            if (!$workflow->can($order, 'process')) {
                // this means that the state was changed another place
                continue;
            }

            $workflow->apply($order, 'process'); // todo use constant
            $manager->flush();

            try {
                $this->client->updateOrder($order);

                // todo use constant
                if (!$workflow->can($order, 'push')) {
                    throw new UnrecoverableMessageHandlingException(sprintf(
                        'Could not apply transition "push" on order with id "%s". Mailchimp state: "%s"',
                        $order->getId(), $order->getMailchimpState()
                    ));
                }

                $workflow->apply($order, 'push'); // todo use constant
            } catch (Throwable $e) {
                $order->setMailchimpError(self::buildErrorMessage($e));
                $workflow->apply($order, 'fail'); // todo use constant
            } finally {
                $manager->flush();
            }
        }
    }

    private function getWorkflow(object $obj): Workflow
    {
        if (null === $this->workflow) {
            $this->workflow = $this->workflowRegistry->get($obj, 'mailchimp'); // todo use constant here
        }

        return $this->workflow;
    }

    private static function buildErrorMessage(Throwable $e): string
    {
        $error = $e->getMessage() . "\n\n";
        if ($e instanceof ClientException) {
            $error .= 'Uri: ' . $e->getUri() . "\n\n";
            $error .= 'Status code: ' . $e->getStatusCode() . "\n\n";
            $error .= "Options:\n" . print_r($e->getOptions(), true) . "\n\n";
        }

        $error .= $e->getTraceAsString();

        return $error;
    }
}
