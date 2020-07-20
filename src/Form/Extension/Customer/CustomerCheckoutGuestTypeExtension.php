<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Extension\Customer;

use Setono\SyliusMailchimpPlugin\Model\ChannelInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerCheckoutGuestType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class CustomerCheckoutGuestTypeExtension extends AbstractTypeExtension
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

        if (!in_array(ChannelInterface::DISPLAY_NEWSLETTER_SUBSCRIBE_STEP_ADDRESSING, $channel->getDisplaySubscribeToNewsletterAtCheckout() ?? [], true)) {
            return;
        }

        $builder->add('subscribedToNewsletter', CheckboxType::class, [
            'label' => 'sylius.form.customer.subscribed_to_newsletter',
            'required' => false,
        ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            /** @var CustomerInterface|null $customer */
            $customer = $event->getData();

            // If a customer is already existing, remove email field
            if (null !== $customer && null !== $customer->getId()) {
                $form->remove('email');
            }
        });
    }

    public static function getExtendedTypes(): array
    {
        return [CustomerCheckoutGuestType::class];
    }
}
