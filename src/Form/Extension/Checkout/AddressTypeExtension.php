<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Extension\Checkout;

use function in_array;
use Setono\SyliusMailchimpPlugin\Model\ChannelInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerCheckoutGuestType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class AddressTypeExtension extends AbstractTypeExtension
{
    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    public static function getExtendedTypes(): array
    {
        return [AddressType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (!in_array(ChannelInterface::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_ADDRESSING, $channel->getDisplaySubscribeToNewsletterAtCheckout() ?? [], true)) {
            return;
        }

        // Add customer form no matter what to be able to subscribe to newsletter
        $builder->add('customer', CustomerCheckoutGuestType::class, ['constraints' => [new Valid()]]);
    }
}
