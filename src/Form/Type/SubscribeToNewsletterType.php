<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SubscribeToNewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'setono_sylius_mailchimp.form.subscribe_to_newsletter.email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_mailchimp_subscribe_to_newsletter';
    }
}
