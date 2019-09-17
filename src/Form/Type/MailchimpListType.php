<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Type;

use Setono\SyliusMailchimpPlugin\Model\MailchimpList;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MailchimpListType extends AbstractType implements EventSubscriberInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('listId', TextType::class, [
                'label' => 'setono_sylius_mailchimp.ui.list_id',
                'attr' => [
                    'autocomplete' => 'off',
                ],
                'required' => true,
            ])
            ->add('exportSubscribedOnly', CheckboxType::class, [
                'label' => 'setono_sylius_mailchimp.ui.export_subscribed_only',
                'required' => false,
            ])

            ->add('storeId', TextType::class, [
                'label' => 'setono_sylius_mailchimp.ui.store_id',
                'required' => false,
            ])
            ->addEventSubscriber($this)
            ->add('channels', ChannelChoiceType::class, [
                'label' => 'sylius.form.payment_method.channels',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MailchimpList::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_mailchimp_list';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $resource = $event->getData();
        $configDisabled = false;
        $disabled = false;
        $required = false;

        if ($resource instanceof MailchimpListInterface) {
            $disabled = null !== $resource->getStoreCurrency();
            $configDisabled = null !== $resource->getConfig();
            $required = null !== $resource->getStoreId();
        } elseif (null !== $resource) {
            throw new UnexpectedTypeException($resource, MailchimpListInterface::class);
        }

        $form = $event->getForm();
        if (!$configDisabled) {
            $form->add('config', MailchimpConfigAutocompleteChoiceType::class, [
                'label' => 'setono_sylius_mailchimp.ui.config',
                'required' => true,
            ]);
        }

        $form->add('storeCurrency', CurrencyChoiceType::class, [
            'label' => 'setono_sylius_mailchimp.ui.store_currency',
            'required' => $required,
            'disabled' => $disabled,
        ]);
    }
}
