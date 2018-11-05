<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Form\Type;

use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MailChimpConfigType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var MailChimpConfigInterface $config */
        $config = $builder->getData();

        $builder
            ->add('code', TextType::class, [
                'label' => 'setono_sylius_mailchimp_export_plugin.ui.code',
                'disabled' => null !== $config->getCode(),
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'setono_sylius_mailchimp_export_plugin.ui.api_key',
            ])
            ->add('exportAll', CheckboxType::class, [
                'label' => 'setono_sylius_mailchimp_export_plugin.ui.export_all_emails',
            ])
            ->add('lists', CollectionType::class, [
                'label' => 'setono_sylius_mailchimp_export_plugin.ui.config',
                'entry_type' => MailChimpListType::class,
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
