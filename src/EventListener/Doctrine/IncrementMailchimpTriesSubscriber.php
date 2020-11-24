<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Setono\SyliusMailchimpPlugin\Model\MailchimpAwareInterface;

final class IncrementMailchimpTriesSubscriber implements EventSubscriber
{
    /** @var string */
    private $mailchimpStateField;

    public function __construct(string $mailchimpStateField = 'mailchimpState')
    {
        $this->mailchimpStateField = $mailchimpStateField;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::preUpdate];
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof MailchimpAwareInterface) {
            return;
        }

        if (!$args->hasChangedField($this->mailchimpStateField)) {
            return;
        }

        // when an entity goes from pending to processing we consider this a try
        if ($args->getOldValue($this->mailchimpStateField) !== MailchimpAwareInterface::MAILCHIMP_STATE_PENDING) {
            return;
        }

        if ($args->getNewValue($this->mailchimpStateField) !== MailchimpAwareInterface::MAILCHIMP_STATE_PROCESSING) {
            return;
        }

        $entity->incrementMailchimpTries();
    }
}
