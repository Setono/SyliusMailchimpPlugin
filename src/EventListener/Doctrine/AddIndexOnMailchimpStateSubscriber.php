<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;
use function Safe\sprintf;
use Setono\SyliusMailchimpPlugin\Model\MailchimpAwareInterface;

final class AddIndexOnMailchimpStateSubscriber implements EventSubscriber
{
    /** @var string */
    private $mailchimpStateField;

    public function __construct(string $mailchimpStateField = 'mailchimpState')
    {
        $this->mailchimpStateField = $mailchimpStateField;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $classMetadata = $event->getClassMetadata();
        $class = $classMetadata->getName();
        if (!is_a($class, MailchimpAwareInterface::class, true)) {
            return;
        }

        if (!$classMetadata->hasField($this->mailchimpStateField)) {
            throw new RuntimeException(sprintf(
                'The class "%s" does not have the field "%s"', $class, $this->mailchimpStateField
            ));
        }

        $column = $classMetadata->getColumnName($this->mailchimpStateField);

        $classMetadata->table = array_merge_recursive([
            'indexes' => [
                [
                    'columns' => [
                        $column,
                    ],
                ],
            ],
        ], $classMetadata->table);
    }
}
