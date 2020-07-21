<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface
{
    public const DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_ADDRESSING = 'addressing';

    public const DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_COMPLETE = 'complete';

    public const DISPLAY_NEWSLETTER_SUBSCRIBE_CHOICES = [
        'setono_sylius_mailchimp.form.channel.display_newsletter_subscribe.' . self::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_ADDRESSING => self::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_ADDRESSING,
        'setono_sylius_mailchimp.form.channel.display_newsletter_subscribe.' . self::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_COMPLETE => self::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_COMPLETE,
    ];

    public function getDisplaySubscribeToNewsletterAtCheckout(): ?array;

    public function setDisplaySubscribeToNewsletterAtCheckout(?array $displaySubscribeToNewsletterAtCheckout): void;
}
