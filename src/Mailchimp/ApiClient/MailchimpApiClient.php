<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient;

use DateTimeZone;
use DrewM\MailChimp\MailChimp as Client;
use DrewM\MailChimp\MailChimp;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use function Safe\substr;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiErrorResponseException;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class MailchimpApiClient implements MailchimpApiClientInterface
{
    /** @var Client */
    private $apiClient;

    public function __construct(Client $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function isApiKeyValid(): bool
    {
        try {
            $this->apiClient->get('/lists');
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function getAudiences(array $options = []): array
    {
        $options = array_merge_recursive([
           'count' => 1000,
        ], $options);

        $res = $this->apiClient->get('/lists', $options);

        if(!$this->apiClient->success()) {
            throw new RuntimeException('Could not get audiences');
        }

        return $res['lists'];
    }

    public function isListIdExists(string $listId): bool
    {
        try {
            $list = $this->apiClient->get(sprintf('/lists/%s', $listId));

            return isset($list['id']);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function isStoreIdExists(string $storeId): bool
    {
        try {
            $store = $this->apiClient->get(sprintf('/ecommerce/stores/%s', $storeId));

            return isset($store['id']);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getMergeFields(string $listId, array $requiredMergeFields): array
    {
        try {
            $response = $this->apiClient->get(
                sprintf('/lists/%s/merge-fields', $listId)
            );

            if (false !== $response) {
                return $response;
            }

            // @todo ?
            return [];
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * @throws StringsException
     * @throws RuntimeException
     */
    public function updateStore(AudienceInterface $audience): void
    {
        /** @var ChannelInterface $channel */
        $channel = $audience->getChannel();

        Assert::isInstanceOf($channel, ChannelInterface::class);

        $storeId = $channel->getCode();
        $currencyCode = self::getBaseCurrencyCode($channel);
        $localeCode = substr(self::getDefaultLocaleCode($channel), 0, 2);

        $data = [
            'list_id' => $audience->getAudienceId(),
            'name' => $channel->getName(),
            'platform' => 'Sylius',
            'domain' => $channel->getHostname(),
            'email_address' => $channel->getContactEmail(),
            'currency_code' => $currencyCode,
            'primary_locale' => $localeCode,
        ];

        $shopBillingData = $channel->getShopBillingData();
        if (null !== $shopBillingData) {
            $data['address']['address1'] = $shopBillingData->getStreet();
            $data['address']['city'] = $shopBillingData->getCity();
            $data['address']['postal_code'] = $shopBillingData->getPostcode();
            $data['address']['country_code'] = $shopBillingData->getCountryCode();

            // this will remove null values from $data['address']
            $data['address'] = array_filter($data['address']);

            $data['timezone'] = self::getTimeZone($shopBillingData->getCountryCode());
        }

        // this will remove null values from $data
        $data = array_filter($data);

        if ($this->isStoreIdExists($storeId)) {
            $res = $this->apiClient->patch(sprintf('/ecommerce/stores/%s', $storeId), $data);
        } else {
            $data['id'] = $storeId;

            $res = $this->apiClient->post('/ecommerce/stores', $data);
        }

        if (!$this->apiClient->success()) {
            throw new RuntimeException('Could not update/create the store on Mailchimp. Errors: ' . self::extractErrorsFromResult($res, true)); // todo better exception
        }
    }

    public function createStore(array $storeData): void
    {
        try {
            $this->apiClient->post(
                '/ecommerce/stores',
                $storeData
            );
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function exportEmail(string $listId, string $email, array $options = []): bool
    {
        try {
            $response = $this->apiClient->post(sprintf('/lists/%s/members', $listId), $options + [
                'email_address' => $email,
                'status' => 'subscribed',
            ]);

            if ($this->isErrorResponse($response)) {
                throw new MailchimpApiErrorResponseException($response);
            }

            return true;
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function updateMember(AudienceInterface $audience, CustomerInterface $customer): void
    {
        $data = [
            'email_address' => $customer->getEmail(),
            'status' => 'subscribed',
            // todo these merge fields are not required to be in mailchimp, so we need to fix this
            'merge_fields' => [
                'FNAME' => $customer->getFirstName(),
                'LNAME' => $customer->getLastName(),
            ]
        ];

        $res = $this->apiClient->put(sprintf('/lists/%s/members/%s', $audience->getAudienceId(), MailChimp::subscriberHash($customer->getEmail())), $data);

        if(!$this->apiClient->success()) {
            throw new RuntimeException(self::extractErrorFromResult($res));
        }
    }


    public function updateEmail(string $listId, string $email, array $options, ?string $oldEmail = null): bool
    {
        try {
            $hash = $this->apiClient->subscriberHash($oldEmail ?: $email);
            $response = $this->apiClient->put(sprintf('/lists/%s/members/%s', $listId, $hash), $options + [
                'email_address' => $email,
            ]);

            if ($this->isErrorResponse($response)) {
                throw new MailchimpApiErrorResponseException($response);
            }

            return true;
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function removeEmail(string $listId, string $email): void
    {
        try {
            $this->apiClient->delete(sprintf(
                '/lists/%s/members/%s',
                $listId,
                $this->apiClient->subscriberHash($email)
            ));
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function exportProduct(string $storeId, array $productData): void
    {
        try {
            $this->apiClient->post(
                sprintf('/ecommerce/stores/%s/products', $storeId),
                $productData
            );
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function exportOrder(string $storeId, array $orderData): void
    {
        try {
            $this->apiClient->post(
                sprintf('/ecommerce/stores/%s/orders', $storeId),
                $orderData
            );
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function removeOrder(string $storeId, string $orderId): void
    {
        try {
            $this->apiClient->delete(sprintf(
                '/ecommerce/stores/%s/orders/%s',
                $storeId,
                $orderId
            ));
        } catch (Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * @param array|false $response
     */
    private function isErrorResponse($response): bool
    {
        return isset($response['type']);
    }

    /**
     * @throws StringsException
     */
    private static function getBaseCurrencyCode(ChannelInterface $channel): string
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

    /**
     * @throws StringsException
     */
    private static function getDefaultLocaleCode(ChannelInterface $channel): string
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

    private static function extractErrorFromResult(array $result): string
    {
        $error = '';
        if(isset($result['title'])) {
            $error .= $result['title'].' ';
        }
        if(isset($result['detail'])) {
            $error .= $result['detail'];
        }

        return $error;
    }

    /**
     * @return array|string
     */
    private static function extractErrorsFromResult(array $result, bool $asString = false)
    {
        $errors = [];

        if (!isset($result['errors']) || !is_array($result['errors'])) {
            return $errors;
        }

        foreach ($result['errors'] as $error) {
            $message = '';
            if (isset($error['field'])) {
                $message .= $error['field'] . ': ';
            }

            $message .= $error['message'];

            $errors[] = $message;
        }

        if ($asString) {
            return implode(', ', $errors);
        }

        return $errors;
    }
}
