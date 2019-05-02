<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MailchimpConfigType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber(null, [
                'label' => 'setono_sylius_mailchimp.ui.code',
            ]))
            ->add('apiKey', TextType::class, [
                'label' => 'setono_sylius_mailchimp.ui.api_key',
            ])
            ->add('lists', CollectionType::class, [
                'label' => 'setono_sylius_mailchimp.ui.lists',
                'entry_type' => MailchimpListType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_mailchimp_export_config';
    }
}
