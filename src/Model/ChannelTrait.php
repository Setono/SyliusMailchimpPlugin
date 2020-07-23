<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Model;

use Doctrine\ORM\Mapping as ORM;
use function in_array;

trait ChannelTrait
{
    /**
     * @var array|string[]|null
     *
     * @ORM\Column(name="display_subscribe_to_newsletter_at_checkout", type="array", nullable=true)
     */
    protected $displaySubscribeToNewsletterAtCheckout;

    public function getDisplaySubscribeToNewsletterAtCheckout(): ?array
    {
        return $this->displaySubscribeToNewsletterAtCheckout;
    }

    public function setDisplaySubscribeToNewsletterAtCheckout(?array $displaySubscribeToNewsletterAtCheckout): void
    {
        foreach ($displaySubscribeToNewsletterAtCheckout as $key => $value) {
            if (!in_array($value, ChannelInterface::DISPLAY_NEWSLETTER_SUBSCRIBE_CHOICES)) {
                unset($displaySubscribeToNewsletterAtCheckout[$key]);
            }
        }
        $this->displaySubscribeToNewsletterAtCheckout = $displaySubscribeToNewsletterAtCheckout;
    }
}
