<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\SynchronizeCustomerBatch;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SynchronizeCustomerBatchHandler implements MessageHandlerInterface
{
    /** @var QueryRebuilderInterface */
    private $queryRebuilder;

    /** @var MailchimpApiClientInterface */
    private $client;

    /** @var AudienceRepositoryInterface */
    private $audienceRepository;
    /**
     * @var ObjectManager
     */
    private $customerManager;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        MailchimpApiClientInterface $client,
        AudienceRepositoryInterface $audienceRepository,
        ObjectManager $customerManager
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->client = $client;
        $this->audienceRepository = $audienceRepository;
        $this->customerManager = $customerManager;
    }

    public function __invoke(SynchronizeCustomerBatch $message)
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());

        /** @var CustomerInterface[] $customers */
        $customers = $q->getResult();

        foreach ($customers as $customer) {
            // todo this is REALLY bad code
            // create a provider that does this magic
            $audience = null;
            foreach ($customer->getOrders() as $order) {
                $channel = $order->getChannel();
                if(null === $channel) {
                    continue;
                }

                $audience = $this->audienceRepository->findOneByChannel($channel);
                if(null !== $audience) {
                    break;
                }
            }

            if(null === $audience) {
                // todo maybe this should fire a warning somewhere
                continue;
            }

            $this->client->updateMember($audience, $customer);
            $customer->setLastMailchimpSync();

            $this->customerManager->flush();
        }
    }
}
