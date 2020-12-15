<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use function Safe\sprintf;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Exception\ClientException;
use Setono\SyliusMailchimpPlugin\Message\Command\PushCustomer;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProviderInterface;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Workflow\MailchimpWorkflow;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Registry;
use Webmozart\Assert\Assert;

final class PushCustomerHandler implements MessageHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var AudienceProviderInterface */
    private $audienceProvider;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var ClientInterface */
    private $client;

    /** @var Registry */
    private $workflowRegistry;

    /** @var LoggerInterface */
    private $logger;

    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AudienceProviderInterface $audienceProvider,
        MessageBusInterface $messageBus,
        ClientInterface $client,
        Registry $workflowRegistry,
        LoggerInterface $logger,
        ManagerRegistry $managerRegistry
    ) {
        $this->customerRepository = $customerRepository;
        $this->audienceProvider = $audienceProvider;
        $this->messageBus = $messageBus;
        $this->client = $client;
        $this->workflowRegistry = $workflowRegistry;
        $this->logger = $logger;
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(PushCustomer $message): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($message->getCustomerId());
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $workflow = $this->workflowRegistry->get($customer, MailchimpWorkflow::NAME);
        if (!$workflow->can($customer, MailchimpWorkflow::TRANSITION_PROCESS)) {
            // this means that the state was changed another place
            return;
        }

        $workflow->apply($customer, MailchimpWorkflow::TRANSITION_PROCESS);

        $manager = $this->managerRegistry->getManagerForClass(get_class($customer));
        if (null === $manager) {
            throw new UnrecoverableMessageHandlingException(sprintf(
                'No object manager available for class %s', get_class($customer)
            ));
        }
        $manager->flush();

        try {
            $audience = $this->audienceProvider->getAudienceFromCustomerOrders($customer);
            if (null === $audience) {
                $audience = $this->audienceProvider->getAudienceFromContext();
            }
            if (null === $audience) {
                // todo maybe this should fire a warning somewhere
                return;
            }

            $this->client->updateMember($audience, $customer);

            if (!$workflow->can($customer, MailchimpWorkflow::TRANSITION_PUSH)) {
                throw new UnrecoverableMessageHandlingException(sprintf(
                    'Could not apply transition "push" on customer with id "%s". Mailchimp state: "%s"',
                    $customer->getId(), $customer->getMailchimpState()
                ));
            }

            $workflow->apply($customer, MailchimpWorkflow::TRANSITION_PUSH);
        } catch (\Throwable $e) {
            $this->logger->error(self::buildErrorMessage($e));
            $customer->setMailchimpError(self::buildErrorMessage($e));
            $workflow->apply($customer, MailchimpWorkflow::TRANSITION_FAIL);
        }
    }

    private static function buildErrorMessage(\Throwable $e): string
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
