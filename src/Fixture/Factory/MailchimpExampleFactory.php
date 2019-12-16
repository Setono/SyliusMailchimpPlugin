<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Fixture\Factory;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailchimpExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var FactoryInterface */
    protected $audienceFactory;

    /** @var AudienceRepositoryInterface */
    protected $audienceRepository;

    /** @var \Faker\Generator */
    protected $faker;

    /** @var OptionsResolver */
    protected $optionsResolver;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        FactoryInterface $audienceFactory,
        AudienceRepositoryInterface $audienceRepository
    ) {
        $this->channelRepository = $channelRepository;
        $this->audienceFactory = $audienceFactory;
        $this->audienceRepository = $audienceRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): AudienceInterface
    {
        return $this->createAudience($options);
    }

    protected function createAudience(array $options): AudienceInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var AudienceInterface|null $audience */
        $audience = $this->audienceRepository->findOneBy(['audienceId' => $options['audience_id']]);
        if (null === $audience) {
            /** @var AudienceInterface $audience */
            $audience = $this->audienceFactory->createNew();
        }

        $audience->setName($options['name']);
        $audience->setAudienceId($options['audience_id']);
        $audience->setChannel($options['channel']);

        return $audience;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                /** @var string $text */
                $text = $this->faker->words(3, true);

                return $text;
            })
            ->setAllowedTypes('name', 'string')

            ->setDefault('audience_id', function (Options $options): string {
                /** @var string $text */
                $text = $this->faker->lexify('??????????');

                return $text;
            })
            ->setAllowedTypes('audience_id', 'string')

            ->setDefault('channel', null)
            ->setAllowedTypes('channel', ['null', 'string', ChannelInterface::class])
            ->setNormalizer('channel', LazyOption::findOneBy($this->channelRepository, 'code'))
        ;
    }
}
