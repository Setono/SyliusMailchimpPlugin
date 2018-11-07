<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Extension;

use Setono\SyliusMailchimpPlugin\Form\Type\CustomerNewsletterSubscriptionType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CompleteTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', CustomerNewsletterSubscriptionType::class)
        ;
    }

    public function getExtendedType(): string
    {
        return CompleteType::class;
    }
}
