<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Extension\Checkout;

use Setono\SyliusMailchimpPlugin\Form\Type\CustomerNewsletterSubscriptionType;
use Setono\SyliusMailchimpPlugin\Model\ChannelInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Webmozart\Assert\Assert;

final class CompleteTypeExtension extends AbstractTypeExtension
{
    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (!in_array(ChannelInterface::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_COMPLETE, $channel->getDisplaySubscribeToNewsletterAtCheckout() ?? [], true)) {
            return;
        }
        $builder->add('customer', CustomerNewsletterSubscriptionType::class);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CompleteType::class];
    }
}
