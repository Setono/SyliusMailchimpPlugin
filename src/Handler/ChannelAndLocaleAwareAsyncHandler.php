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

abstract class ChannelAndLocaleAwareAsyncHandler implements ChannelAndLocaleAwareHandlerInterface, PsrProcessor, TopicSubscriberInterface
{
    private const ID = 'id';
    private const CHANNEL = 'channel';
    private const LOCALE = 'locale';

    /** @var ProducerInterface */
    protected $producer;

    /** @var ChannelAndLocaleAwareHandlerInterface */
    protected $handler;

    /** @var EntityRepository */
    protected $repository;

    /** @var EntityManager */
    protected $entityManager;

    /**
     * @param ProducerInterface $producer
     * @param ChannelAndLocaleAwareHandlerInterface $handler
     * @param EntityRepository $repository
     * @param EntityManager $entityManager
     */
    public function __construct(
        ProducerInterface $producer,
        ChannelAndLocaleAwareHandlerInterface $handler,
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
    public function handle(ResourceInterface $resource, string $channelCode, string $localeCode): void
    {
        Assert::isInstanceOf($resource, $this->repository->getClassName());

        $this->producer->sendEvent(
            self::getEventName(),
            [
                self::ID => $resource->getId(),
                self::CHANNEL => $channelCode,
                self::LOCALE => $localeCode,
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
                $body[self::LOCALE]
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
     *
     * @return bool
     */
    private function validateBody($body): bool
    {
        return is_array($body) &&
            array_key_exists(self::ID, $body) &&
            array_key_exists(self::CHANNEL, $body) &&
            array_key_exists(self::LOCALE, $body)
            ;
    }

    /**
     * @return string
     */
    abstract public static function getEventName(): string;
}
