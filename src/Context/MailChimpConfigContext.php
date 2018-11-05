<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Context;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpListInterface;
use Setono\SyliusMailChimpPlugin\Repository\MailChimpConfigRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class MailChimpConfigContext implements MailChimpConfigContextInterface
{
    /** @var MailChimpConfigRepositoryInterface */
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
        MailChimpConfigRepositoryInterface $mailChimpConfigRepository,
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

    public function getConfig(): MailChimpConfigInterface
    {
        $config = $this->mailChimpConfigRepository->findConfig();

        if (null === $config) {
            /** @var MailChimpConfigInterface $config */
            $config = $this->mailChimpConfigFactory->createNew();

            $config->setCode(self::DEFAULT_CODE);

            $this->mailChimpConfigRepository->add($config);
        }

        $this->resolveDefaultLists($config);

        return $config;
    }

    public function isFullySetUp(): bool
    {
        $config = $this->getConfig();

        if (null === $config->getApiKey()) {
            return false;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        if (null === $config->getListForChannelAndLocale($channel, $locale)) {
            return false;
        }

        return true;
    }

    private function resolveDefaultLists(MailChimpConfigINterface $config): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();

        if (null === $config->getListForChannelAndLocale($channel, $locale)) {
            /** @var MailChimpListInterface $list */
            $list = $this->mailChimpListFactory->createNew();

            $list->setConfig($config);
            $list->addChannel($channel);
            $list->addLocale($locale);
            $config->addList($list);
            $list->setListId(uniqid($config->getCode()));

            $this->mailChimpListRepository->add($list);
            $this->configEntityManager->flush();
        }
    }
}
