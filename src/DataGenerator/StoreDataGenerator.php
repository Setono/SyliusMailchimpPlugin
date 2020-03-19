<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DataGenerator;

use DateTimeZone;
use Safe\Exceptions\StringsException;
use function Safe\substr;
use Setono\SyliusMailchimpPlugin\DTO\StoreData;
use Setono\SyliusMailchimpPlugin\Event\StoreDataGeneratedEvent;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class StoreDataGenerator extends DataGenerator implements StoreDataGeneratorInterface
{
    /**
     * @throws StringsException
     */
    public function generate(AudienceInterface $audience): StoreData
    {
        /** @var ChannelInterface|null $channel */
        $channel = $audience->getChannel();

        Assert::isInstanceOf($channel, ChannelInterface::class);

        $data = [
            'id' => $channel->getCode(),
            'list_id' => $audience->getAudienceId(),
            'name' => $channel->getName(),
            'platform' => 'Sylius',
            'domain' => $channel->getHostname(),
            'email_address' => $channel->getContactEmail(),
            'currency_code' => self::getBaseCurrencyCode($channel),
            'primary_locale' => substr(self::getDefaultLocaleCode($channel), 0, 2),
        ];

        $shopBillingData = $channel->getShopBillingData();
        if (null !== $shopBillingData) {
            $data['address']['address1'] = $shopBillingData->getStreet();
            $data['address']['city'] = $shopBillingData->getCity();
            $data['address']['postal_code'] = $shopBillingData->getPostcode();
            $data['address']['country_code'] = $shopBillingData->getCountryCode();

            $data['timezone'] = self::getTimeZone($shopBillingData->getCountryCode());
        }

        $storeData = new StoreData($data);

        $this->eventDispatcher->dispatch(new StoreDataGeneratedEvent($storeData, [
            'audience' => $audience,
            'channel' => $channel,
        ]));

        return $storeData;
    }

    /**
     * todo This is not the best way to do this, but it works for now
     */
    private static function getTimeZone(?string $countryCode): ?string
    {
        if (null === $countryCode) {
            return null;
        }

        $identifiers = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);

        return count($identifiers) > 0 ? $identifiers[0] : null;
    }
}
