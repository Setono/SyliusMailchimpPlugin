<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Client;

use DrewM\MailChimp\MailChimp;
use RuntimeException;
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
            throw new ClientException($uri, $options, $this->httpClient->getLastResponse());
        }

        return $res;
    }

    public function getAudiences(array $options = []): array
    {
        $options = array_merge_recursive([
            'count' => 1000,
        ], $options);

        return $this->makeRequest('get', '/lists', $options)['lists'];
    }

    public function updateOrder(OrderInterface $order): void
    {
        $channel = $order->getChannel();
        Assert::notNull($channel);

        $data = $this->orderDataGenerator->generate($order);
        $orderId = $data['id'];
        $storeId = $channel->getCode();
        Assert::notNull($storeId);

        $this->ensureProductsExist($channel, $order);

        if ($this->hasOrder($storeId, $orderId)) {
            $this->makeRequest('patch', sprintf('/ecommerce/stores/%s/orders/%s', $storeId, $orderId), $data);
        } else {
            $this->makeRequest('post', sprintf('/ecommerce/stores/%s/orders', $storeId), $data);
        }
    }

    public function updateStore(AudienceInterface $audience): void
    {
        $data = $this->storeDataGenerator->generate($audience);
        $storeId = $data['id'];

        if ($this->hasStore($storeId)) {
            unset($data['id']);

            $this->makeRequest('patch', sprintf('/ecommerce/stores/%s', $storeId), $data);
        } else {
            $this->makeRequest('post', '/ecommerce/stores', $data);
        }
    }

    public function updateMember(AudienceInterface $audience, CustomerInterface $customer): void
    {
        Assert::notNull($customer->getEmail());

        if (null === $customer->getFirstName() || null === $customer->getLastName()) {
            $this->subscribeEmail($audience, $customer->getEmail());

            return;
        }

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

    private function createProduct(ChannelInterface $channel, ProductInterface $product, ProductVariantInterface $productVariant = null): void
    {
        $data = $this->productDataGenerator->generate($product, $channel, $productVariant);

        $this->makeRequest('post', sprintf('/ecommerce/stores/%s/products', $channel->getCode()), $data);
    }

    private function hasProduct(ChannelInterface $channel, ProductInterface $product): bool
    {
        try {
            $this->makeRequest('get', sprintf('/ecommerce/stores/%s/products/%s', $channel->getCode(), $product->getCode()));

            return true;
        } catch (ClientException $e) {
            if ($e->getStatusCode() === 404) {
                return false;
            }

            throw $e;
        }
    }

    private function createProductVariant(ChannelInterface $channel, ProductVariantInterface $productVariant): void
    {
        $data = $this->productVariantDataGenerator->generate($productVariant, $channel);

        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $this->makeRequest(
            'post',
            sprintf('/ecommerce/stores/%s/products/%s/variants', $channel->getCode(), $product->getCode()),
            $data
        );
    }

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
