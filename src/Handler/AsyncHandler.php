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

abstract class AsyncHandler implements HandlerInterface, PsrProcessor, TopicSubscriberInterface
{
    /** @var ProducerInterface */
    protected $producer;

    /** @var HandlerInterface */
    protected $handler;

    /** @var EntityRepository */
    protected $repository;

    /** @var EntityManager */
    protected $entityManager;

    /**
     * @param ProducerInterface $producer
     * @param HandlerInterface $handler
     * @param EntityRepository $repository
     * @param EntityManager $entityManager
     */
    public function __construct(
        ProducerInterface $producer,
        HandlerInterface $handler,
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
    public function handle(ResourceInterface $resource): void
    {
        Assert::isInstanceOf($resource, $this->repository->getClassName());

        $this->producer->sendEvent(
            self::getEventName(),
            $resource->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $session)
    {
        /** @var ResourceInterface|null $resource */
        $resource = $this->repository->find(
            $message->getBody()
        );

        if (null === $resource) {
            return self::REJECT;
        }

        try {
            $this->handler->handle($resource);

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
     * @return string
     */
    abstract public static function getEventName(): string;
}
