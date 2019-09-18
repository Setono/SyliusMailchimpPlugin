<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Doctrine\ORM\EntityManager;
use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class CustomerMergefieldsUpdateAsyncHandler implements CustomerMergefieldsUpdateHandlerInterface, PsrProcessor, TopicSubscriberInterface
{
    private const ID = 'id';

    private const CHANNEL = 'channel';

    private const OLD_CUSTOMER_EMAIL = 'old-customer-email';

    /** @var ProducerInterface */
    private $producer;

    /** @var CustomerMergefieldsUpdateHandlerInterface */
    private $handler;

    /** @var EntityRepository */
    private $repository;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(
        ProducerInterface $producer,
        CustomerMergefieldsUpdateHandlerInterface $handler,
        EntityRepository $repository,
        EntityManager $entityManager
    ) {
        $this->producer = $producer;
        $this->repository = $repository;
        $this->handler = $handler;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResourceInterface $resource, string $channelCode, ?string $oldCustomerEmail = null): void
    {
        Assert::isInstanceOf($resource, $this->repository->getClassName());

        $this->producer->sendEvent(
            self::getEventName(),
            [
                self::ID => $resource->getId(),
                self::CHANNEL => $channelCode,
                self::OLD_CUSTOMER_EMAIL => $oldCustomerEmail,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $session)
    {
        /** @var array $body */
        $body = $message->getBody();

        if (!$this->validateBody($body)) {
            return self::REJECT;
        }

        /** @var ResourceInterface $resource */
        $resource = $this->repository->find(
            $body[self::ID]
        );

        if (!$resource instanceof ResourceInterface) {
            return self::REJECT;
        }

        try {
            $this->handler->handle(
                $resource,
                $body[self::CHANNEL],
                $body[self::OLD_CUSTOMER_EMAIL]
            );

            $this->entityManager->flush();
            $this->entityManager->clear();

            return self::ACK;
        } catch (\Exception $e) {
            return self::REJECT;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics()
    {
        return [
            self::getEventName(),
        ];
    }

    /**
     * @param array|mixed $body
     */
    private function validateBody($body): bool
    {
        return is_array($body) &&
            array_key_exists(self::ID, $body) &&
            array_key_exists(self::CHANNEL, $body) &&
            array_key_exists(self::OLD_CUSTOMER_EMAIL, $body)
            ;
    }

    public static function getEventName(): string
    {
        return 'setono_sylius_mailchimp_customer_mergefields_update';
    }
}
