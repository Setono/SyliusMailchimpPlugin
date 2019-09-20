<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Exception;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

/**
 * When an audience is updated - this could be when you want to associate a channel with an audience - this
 * event subscriber will listen to that event and call the Mailchimp API with an 'update store'.
 * If this errors it will effectively stop the updating of the audience and output the error to the user
 */
final class UpdateStoreSubscriber implements EventSubscriberInterface
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'setono_sylius_mailchimp.audience.pre_update' => 'update',
        ];
    }

    public function update(ResourceControllerEvent $event): void
    {
        /** @var AudienceInterface|null $audience */
        $audience = $event->getSubject();

        Assert::isInstanceOf($audience, AudienceInterface::class);

        $channel = $audience->getChannel();
        if (null === $channel) {
            return;
        }

        try {
            $this->client->updateStore($audience);
        } catch (Exception $e) {
            $event->stop($e->getMessage());
        }
    }
}
