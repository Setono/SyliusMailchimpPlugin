<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Type;

use Setono\SyliusMailchimpPlugin\Model\MailchimpExport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MailchimpExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('list', MailchimpListAutocompleteChoiceType::class, [
                'label' => 'setono_sylius_mailchimp.ui.list',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MailchimpExport::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_mailchimp_export';
    }
}
