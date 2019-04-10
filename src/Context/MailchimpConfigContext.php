<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Context;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpConfigRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class MailchimpConfigContext implements MailchimpConfigContextInterface
{
    /** @var MailchimpConfigRepositoryInterface */
    private $mailChimpConfigRepository;

    /** @var RepositoryInterface */
    private $mailChimpListRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var FactoryInterface */
    private $mailChimpConfigFactory;

    /** @var FactoryInterface */
    private $mailChimpListFactory;

    /** @var EntityManagerInterface */
    private $configEntityManager;

    public function __construct(
        MailchimpConfigRepositoryInterface $mailChimpConfigRepository,
        RepositoryInterface $mailChimpListRepository,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        FactoryInterface $mailChimpConfigFactory,
        FactoryInterface $mailChimpListFactory,
        EntityManagerInterface $configEntityManager
    ) {
        $this->mailChimpConfigRepository = $mailChimpConfigRepository;
        $this->mailChimpListRepository = $mailChimpListRepository;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->mailChimpConfigFactory = $mailChimpConfigFactory;
        $this->mailChimpListFactory = $mailChimpListFactory;
        $this->configEntityManager = $configEntityManager;
    }

    public function getConfig(): ?MailchimpConfigInterface
    {
        return $this->mailChimpConfigRepository->findOneActive();
    }

    public function isFullySetUp(): bool
    {
        $config = $this->getConfig();

        if (!$config instanceof MailchimpConfigInterface) {
            return false;
        }

        if (null === $config->getApiKey()) {
            return false;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        return !(null === $config->getListForChannelAndLocale($channel, $locale));
    }
}
