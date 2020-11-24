<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Safe\DateTime;
use Setono\SyliusMailchimpPlugin\Model\MailchimpAwareInterface;

final class UpdateMailchimpUpdatedAtSubscriber implements EventSubscriber
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

        $entity->setMailchimpStateUpdatedAt(new DateTime());
    }
}
