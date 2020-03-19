<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Client;

use DrewM\MailChimp\MailChimp;
use RuntimeException;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Setono\SyliusMailchimpPlugin\DataGenerator\OrderDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DataGenerator\ProductDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DataGenerator\ProductVariantDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DataGenerator\StoreDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\Exception\ClientException;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class Client implements ClientInterface
{
    /** @var MailChimp */
    private $httpClient;

    /** @var StoreDataGeneratorInterface */
    private $storeDataGenerator;

    /** @var OrderDataGeneratorInterface */
    private $orderDataGenerator;

    /** @var ProductDataGeneratorInterface */
    private $productDataGenerator;

    /** @var ProductVariantDataGeneratorInterface */
    private $productVariantDataGenerator;

    public function __construct(
        MailChimp $httpClient,
        StoreDataGeneratorInterface $storeDataGenerator,
        OrderDataGeneratorInterface $orderDataGenerator,
        ProductDataGeneratorInterface $productDataGenerator,
        ProductVariantDataGeneratorInterface $productVariantGenerator
    ) {
        $this->httpClient = $httpClient;
        $this->storeDataGenerator = $storeDataGenerator;
        $this->orderDataGenerator = $orderDataGenerator;
        $this->productDataGenerator = $productDataGenerator;
        $this->productVariantDataGenerator = $productVariantGenerator;
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function makeRequest(string $method, string $uri, array $options = []): array
    {
        $callable = [$this->httpClient, $method];
        if (!is_callable($callable)) {
            throw new RuntimeException(sprintf(
                'The method "%s" does not exist on the http client "%s"', $method, get_class($this->httpClient)
            ));
        }
        $res = $callable($uri, $options);

        if (!$this->httpClient->success()) {
            throw new ClientException($this->httpClient->getLastResponse());
        }

        return $res;
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    public function getAudiences(array $options = []): array
    {
        $options = array_merge_recursive([
            'count' => 1000,
        ], $options);

        return $this->makeRequest('get', '/lists', $options)['lists'];
    }

    /**
     * @throws StringsException
     * @throws JsonException
     */
    public function updateOrder(OrderInterface $order): void
    {
        $channel = $order->getChannel();
        Assert::notNull($channel);

        $storeId = $channel->getCode();

        $orderData = $this->orderDataGenerator->generate($order);

        $this->ensureProductsExist($channel, $order);

        if ($this->hasOrder($storeId, $orderData->id)) {
            $this->makeRequest(
                'patch',
                sprintf('/ecommerce/stores/%s/orders/%s', $storeId, $orderData->id),
                $orderData->toArray()
            );
        } else {
            $this->makeRequest(
                'post',
                sprintf('/ecommerce/stores/%s/orders', $storeId),
                $orderData->toArray()
            );
        }
    }

    /**
     * @throws StringsException
     * @throws JsonException
     */
    public function updateStore(AudienceInterface $audience): void
    {
        $storeData = $this->storeDataGenerator->generate($audience);
        $storeId = $storeData->id;

        if ($this->hasStore($storeId)) {
            unset($storeData->id);

            $this->makeRequest('patch', sprintf('/ecommerce/stores/%s', $storeId), $storeData->toArray());
        } else {
            $this->makeRequest('post', '/ecommerce/stores', $storeData->toArray());
        }
    }

    /**
     * @throws StringsException
     * @throws JsonException
     */
    public function updateMember(AudienceInterface $audience, CustomerInterface $customer): void
    {
        $data = [
            'email_address' => $customer->getEmail(),
            'status' => 'subscribed',
            // todo these merge fields are not required to be in mailchimp, so we need to fix this
            'merge_fields' => [
                'FNAME' => $customer->getFirstName(),
                'LNAME' => $customer->getLastName(),
            ],
        ];

        $this->makeRequest('put',
            sprintf(
                '/lists/%s/members/%s',
                $audience->getAudienceId(),
                MailChimp::subscriberHash($customer->getEmail())
            ),
            $data
        );
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    public function subscribeEmail(AudienceInterface $audience, string $email): void
    {
        $data = [
            'email_address' => $email,
            'status' => 'subscribed',
        ];

        $this->makeRequest('put',
            sprintf(
                '/lists/%s/members/%s',
                $audience->getAudienceId(),
                MailChimp::subscriberHash($email)
            ),
            $data
        );
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasOrder(string $storeId, string $orderId): bool
    {
        try {
            $this->makeRequest('get', sprintf('/ecommerce/stores/%s/orders/%s', $storeId, $orderId));

            return true;
        } catch (ClientException $e) {
            if ($e->getStatusCode() === 404) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasStore(string $storeId): bool
    {
        try {
            $this->makeRequest('get', sprintf('/ecommerce/stores/%s', $storeId));

            return true;
        } catch (ClientException $e) {
            if ($e->getStatusCode() === 404) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function createProduct(
        ChannelInterface $channel,
        ProductInterface $product,
        ProductVariantInterface $productVariant = null
    ): void {
        $productData = $this->productDataGenerator->generate($product, $channel, $productVariant);

        $this->makeRequest(
            'post',
            sprintf('/ecommerce/stores/%s/products', $channel->getCode()),
            $productData->toArray()
        );
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasProduct(ChannelInterface $channel, ProductInterface $product): bool
    {
        try {
            $this->makeRequest(
                'get',
                sprintf('/ecommerce/stores/%s/products/%s', $channel->getCode(), $product->getCode())
            );

            return true;
        } catch (ClientException $e) {
            if ($e->getStatusCode() === 404) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function createProductVariant(ChannelInterface $channel, ProductVariantInterface $productVariant): void
    {
        $productVariantData = $this->productVariantDataGenerator->generate($productVariant, $channel);

        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $this->makeRequest(
            'post',
            sprintf('/ecommerce/stores/%s/products/%s/variants', $channel->getCode(), $product->getCode()),
            $productVariantData->toArray()
        );
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasProductVariant(ChannelInterface $channel, ProductVariantInterface $productVariant): bool
    {
        $product = $productVariant->getProduct();
        Assert::notNull($product);

        try {
            $this->makeRequest('get', sprintf(
                '/ecommerce/stores/%s/products/%s/variants/%s',
                $channel->getCode(), $product->getCode(), $productVariant->getCode()
            ));

            return true;
        } catch (ClientException $e) {
            if ($e->getStatusCode() === 404) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function ensureProductsExist(ChannelInterface $channel, OrderInterface $order): void
    {
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();
            $product = $orderItem->getProduct();

            if (null === $variant || null === $product) {
                continue;
            }

            if (!$this->hasProduct($channel, $product)) {
                $this->createProduct($channel, $product, $variant);
            }

            if (!$this->hasProductVariant($channel, $variant)) {
                $this->createProductVariant($channel, $variant);
            }
        }
    }
}
