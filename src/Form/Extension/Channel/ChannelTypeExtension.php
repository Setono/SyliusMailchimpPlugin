<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Extension\Channel;

use Setono\SyliusMailchimpPlugin\Model\ChannelInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('displaySubscribeToNewsletterAtCheckout', ChoiceType::class, [
            'label' => 'setono_sylius_mailchimp.form.channel.display_subscribe_to_newsletter_at_checkout',
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'choices' => ChannelInterface::DISPLAY_NEWSLETTER_SUBSCRIBE_CHOICES,
        ]);
    }

    public static function getExtendedTypes(): array
    {
        return [ChannelType::class];
    }
}
