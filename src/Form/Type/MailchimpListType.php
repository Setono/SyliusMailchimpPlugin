<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Type;

use Setono\SyliusMailchimpPlugin\Entity\MailchimpList;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MailchimpListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('listId', TextType::class, [
                'label' => 'setono_sylius_mailchimp_export_plugin.ui.list_id',
                'required' => true,
            ])
            ->add('channels', ChannelChoiceType::class, [
                'label' => 'sylius.form.payment_method.channels',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('locales', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locales',
                'required' => true,
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MailchimpList::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_mailchimp_export_list';
    }
}
