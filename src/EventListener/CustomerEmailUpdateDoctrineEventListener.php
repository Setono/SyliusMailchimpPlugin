<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusMailchimpPlugin\Handler\CustomerMergefieldsUpdateHandlerInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerEmailUpdateDoctrineEventListener
{
    /** @var CustomerMergefieldsUpdateHandlerInterface */
    protected $customerMergefieldsUpdateHandler;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var array */
    private $customersToUpdate = [];

    public function __construct(
        CustomerMergefieldsUpdateHandlerInterface $customerMergefieldsUpdateHandler,
        ChannelContextInterface $channelContext
    ) {
        $this->customerMergefieldsUpdateHandler = $customerMergefieldsUpdateHandler;
        $this->channelContext = $channelContext;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof CustomerInterface) {
            return;
        }

        if ($args->hasChangedField('emailCanonical')) {
            $oldCustomerEmail = $args->getOldValue('emailCanonical');
            $this->customersToUpdate[$oldCustomerEmail] = $entity;
        } elseif ($args->hasChangedField('firstName') || $args->hasChangedField('lastName') ) {
            $this->customersToUpdate[$entity->getEmailCanonical()] = $entity;
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        /** @var CustomerInterface $customer */
        foreach ($this->customersToUpdate as $oldCustomerEmail => $customer) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            $this->customerMergefieldsUpdateHandler->handle(
                $customer,
                $channel->getCode(),
                $oldCustomerEmail === $customer->getEmailCanonical() ? null : $oldCustomerEmail
            );
        }
    }
}
