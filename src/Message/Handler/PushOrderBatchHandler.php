<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Safe\DateTime;
use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushOrderBatch;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PushOrderBatchHandler implements MessageHandlerInterface
{
    /** @var QueryRebuilderInterface */
    private $queryRebuilder;

    /** @var ClientInterface */
    private $client;

    /** @var ObjectManager */
    private $orderManager;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        ClientInterface $client,
        ObjectManager $orderManager
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->client = $client;
        $this->orderManager = $orderManager;
    }

    public function __invoke(PushOrderBatch $message): void
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());

        /** @var OrderInterface[] $orders */
        $orders = $q->getResult();

        foreach ($orders as $order) {
            $this->client->updateOrder($order);

            $now = new DateTime();
            $order->setPushedToMailchimp($now);

            // update the updated at manually so that we are sure
            // it will be the same value as the pushed to mailchimp value
            $order->setUpdatedAt($now);

            $this->orderManager->flush();
        }
    }
}
