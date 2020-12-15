<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use function Safe\sprintf;
use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Exception\ClientException;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomerBatch;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProviderInterface;
use Setono\SyliusMailchimpPlugin\Workflow\MailchimpWorkflow;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;
use Throwable;

final class PushCustomerBatchHandler implements MessageHandlerInterface
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

    /** @var LoggerInterface */
    private $logger;

    /** @var AudienceProviderInterface */
    private $audienceProvider;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        ClientInterface $client,
        ManagerRegistry $managerRegistry,
        Registry $workflowRegistry,
        LoggerInterface $logger,
        AudienceProviderInterface $audienceProvider
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->client = $client;
        $this->managerRegistry = $managerRegistry;
        $this->workflowRegistry = $workflowRegistry;
        $this->logger = $logger;
        $this->audienceProvider = $audienceProvider;
    }

    public function __invoke(PushCustomerBatch $message): void
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());
        $manager = $this->managerRegistry->getManagerForClass($message->getBatch()->getClass());
        if (null === $manager) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'No object manager available for class %s', $message->getBatch()->getClass()
            ));
        }

        /** @var CustomerInterface[] $customers */
        $customers = $q->getResult();

        foreach ($customers as $customer) {
            $workflow = $this->getWorkflow($customer);

            if (!$workflow->can($customer, MailchimpWorkflow::TRANSITION_PROCESS)) {
                // this means that the state was changed another place
                continue;
            }

            $workflow->apply($customer, MailchimpWorkflow::TRANSITION_PROCESS);
            $manager->flush();

            try {
                $audience = $this->audienceProvider->getAudienceFromCustomerOrders($customer);
                if (null === $audience) {
                    $audience = $this->audienceProvider->getAudienceFromContext();
                }
                if (null === $audience) {
                    // todo maybe this should fire a warning somewhere
                    continue;
                }

                $this->client->updateMember($audience, $customer);

                if (!$workflow->can($customer, MailchimpWorkflow::TRANSITION_PUSH)) {
                    throw new UnrecoverableMessageHandlingException(sprintf(
                        'Could not apply transition "push" on customer with id "%s". Mailchimp state: "%s"',
                        $customer->getId(), $customer->getMailchimpState()
                    ));
                }

                $workflow->apply($customer, MailchimpWorkflow::TRANSITION_PUSH);
            } catch (Throwable $e) {
                $this->logger->error(self::buildErrorMessage($e));
                $customer->setMailchimpError(self::buildErrorMessage($e));
                $workflow->apply($customer, MailchimpWorkflow::TRANSITION_FAIL);
            } finally {
                $manager->flush();
            }
        }
    }

    private function getWorkflow(object $obj): Workflow
    {
        if (null === $this->workflow) {
            $this->workflow = $this->workflowRegistry->get($obj, MailchimpWorkflow::NAME); // todo use constant here
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
