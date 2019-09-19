<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Setono\DoctrineORMBatcher\Query\QueryRebuilderInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\PushOrderBatch;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PushOrderBatchHandler implements MessageHandlerInterface
{
    /** @var QueryRebuilderInterface */
    private $queryRebuilder;

    /** @var MailchimpApiClientInterface */
    private $client;

    public function __construct(
        QueryRebuilderInterface $queryRebuilder,
        MailchimpApiClientInterface $client
    ) {
        $this->queryRebuilder = $queryRebuilder;
        $this->client = $client;
    }

    public function __invoke(PushOrderBatch $message)
    {
        $q = $this->queryRebuilder->rebuild($message->getBatch());

        /** @var OrderInterface[] $orders */
        $orders = $q->getResult();

        foreach ($orders as $order) {
            dump($order);
        }
    }
}
