<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Fixture\Factory;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpConfigRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Factory\MailchimpListFactoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MailchimpConfigExampleFactory extends AbstractExampleFactory
{
    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    /** @var OptionsResolver */
    private $listOptionsResolver;

    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var EntityRepository */
    protected $currencyRepository;

    /** @var EntityRepository */
    protected $localeRepository;

    /** @var FactoryInterface */
    protected $mailchimpConfigFactory;

    /** @var MailchimpConfigRepositoryInterface */
    protected $mailchimpConfigRepository;

    /** @var MailchimpListFactoryInterface */
    protected $mailchimpListFactory;

    /** @var MailchimpListRepositoryInterface */
    protected $mailchimpListRepository;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        EntityRepository $currencyRepository,
        EntityRepository $localeRepository,
        FactoryInterface $mailchimpConfigFactory,
        MailchimpConfigRepositoryInterface $mailchimpConfigRepository,
        MailchimpListFactoryInterface $mailchimpListFactory,
        MailchimpListRepositoryInterface $mailchimpListRepository
    ) {
        $this->channelRepository = $channelRepository;
        $this->currencyRepository = $currencyRepository;
        $this->localeRepository = $localeRepository;
        $this->mailchimpConfigFactory = $mailchimpConfigFactory;
        $this->mailchimpConfigRepository = $mailchimpConfigRepository;
        $this->mailchimpListFactory = $mailchimpListFactory;
        $this->mailchimpListRepository = $mailchimpListRepository;

        $this->faker = \Faker\Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);

        $this->listOptionsResolver = new OptionsResolver();
        $this->configureListOptions($this->listOptionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('code')
            ->setAllowedTypes('code', 'string')

            ->setRequired('api_key')
            ->setDefault('api_key', function () {
                return $this->faker->uuid;
            })
            ->setAllowedTypes('api_key', 'string')

            ->setRequired('lists')
            ->setAllowedTypes('lists', 'array')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureListOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('name')
            ->setDefault('name', function (Options $options): string {
                return $this->faker->words(3, true);
            })

            ->setRequired('list_id')
            ->setDefault('list_id', function () {
                return $this->faker->uuid;
            })
            ->setAllowedTypes('list_id', 'string')

            ->setRequired('export_subscribed_only')
            ->setDefault('export_subscribed_only', function () {
                return $this->faker->boolean(30);
            })
            ->setAllowedTypes('export_subscribed_only', 'boolean')

            ->setDefault('store_id', null)
            ->setAllowedTypes('store_id', ['null', 'string'])

            ->setDefault('store_currency', null)
            ->setAllowedTypes('store_currency', ['null', 'string'])
            ->setNormalizer('store_currency', LazyOption::findOneBy($this->currencyRepository, 'code'))

            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): MailchimpConfigInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var MailchimpConfigInterface $mailchimpConfig */
        $mailchimpConfig = $this->mailchimpConfigFactory->createNew();
        $mailchimpConfig->setCode($options['code']);
        $mailchimpConfig->setApiKey($options['api_key']);
        $this->mailchimpConfigRepository->add($mailchimpConfig);

        $this->createLists($mailchimpConfig, $options);

        return $mailchimpConfig;
    }

    /**
     * @param MailchimpConfigInterface $mailchimpConfig
     * @param array $options
     */
    private function createLists(MailchimpConfigInterface $mailchimpConfig, array $options): void
    {
        foreach ($options['lists'] as $listOptions) {
            $listOptions = $this->listOptionsResolver->resolve($listOptions);

            $mailchimpList = $this->mailchimpListFactory->createForMailchimpConfig($mailchimpConfig);
            $mailchimpList->setName($listOptions['name']);
            $mailchimpList->setListId($listOptions['list_id']);
            $mailchimpList->setExportSubscribedOnly($listOptions['export_subscribed_only']);

            $mailchimpList->setStoreId($listOptions['store_id']);
            $mailchimpList->setStoreCurrency($listOptions['store_currency']);
            foreach ($listOptions['channels'] as $channel) {
                $mailchimpList->addChannel($channel);
            }

            $this->mailchimpListRepository->add($mailchimpList);
        }
    }
}
