<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class DataGenerator implements DataGeneratorInterface
{
    /**
     * @throws StringsException
     */
    protected static function generateUrl(
        UrlGeneratorInterface $urlGenerator,
        ChannelInterface $channel,
        string $route,
        array $parameters = []
    ): string {
        $context = $urlGenerator->getContext();
        $context->setHost($channel->getHostname());

        /**
         * When we generate URLs we use the default locale since Mailchimp doesn't use translations on stores
         * We have chosen the default locale as the translation locale since it makes most sense
         */
        $parameters = array_merge([
            '_locale' => self::getDefaultLocaleCode($channel),
        ], $parameters);

        return $urlGenerator->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @throws StringsException
     */
    protected static function getDefaultLocaleCode(ChannelInterface $channel): string
    {
        $locale = $channel->getDefaultLocale();
        if (null === $locale) {
            throw new InvalidArgumentException(sprintf('No default locale set for channel %s', $channel->getCode()));
        }

        $code = $locale->getCode();

        if (null === $code) {
            throw new InvalidArgumentException(sprintf('No code set for locale with id %s', $locale->getId()));
        }

        return $code;
    }

    /**
     * @throws StringsException
     */
    protected static function getBaseCurrencyCode(ChannelInterface $channel): string
    {
        $currency = $channel->getBaseCurrency();
        if (null === $currency) {
            throw new InvalidArgumentException(sprintf('No base currency set for channel %s', $channel->getCode()));
        }

        $code = $currency->getCode();

        if (null === $code) {
            throw new InvalidArgumentException(sprintf('No code set for currency with id %s', $currency->getId()));
        }

        return $code;
    }

    protected static function filterArrayRecursively(array $array): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $val = self::filterArrayRecursively($item);
            } else {
                $val = $item;
            }

            $res[$key] = $val;
        }

        return array_filter($res, static function ($elm) {
            return null !== $elm;
        });
    }
}
