<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Client;

use DrewM\MailChimp\MailChimp;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use function Safe\json_decode;
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
     */
    public function getAudiences(array $options = []): array
    {
        $options = array_merge_recursive([
            'count' => 1000,
        ], $options);

        $res = $this->httpClient->get('/lists', $options);

        if (!$this->httpClient->success()) {
            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }

        return $res['lists'];
    }

    /**
     * @throws StringsException
     * @throws JsonException
     */
    public function updateOrder(OrderInterface $order): void
    {
        $channel = $order->getChannel();
        Assert::notNull($channel);

        $data = $this->orderDataGenerator->generate($order);
        $orderId = $data['id'];
        $storeId = $channel->getCode();

        $this->ensureProductsExist($channel, $order);

        if ($this->hasOrder($storeId, $orderId)) {
            $this->httpClient->patch(sprintf('/ecommerce/stores/%s/orders/%s', $storeId, $orderId), $data);
        } else {
            $this->httpClient->post(sprintf('/ecommerce/stores/%s/orders', $storeId), $data);
        }

        if (!$this->httpClient->success()) {
            dump($data);
            dd($this->httpClient->getLastResponse());

            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }
    }

    /**
     * @throws StringsException
     * @throws JsonException
     */
    public function updateStore(AudienceInterface $audience): void
    {
        $data = $this->storeDataGenerator->generate($audience);
        $storeId = $data['id'];

        if ($this->hasStore($storeId)) {
            unset($data['id']);

            $this->httpClient->patch(sprintf('/ecommerce/stores/%s', $storeId), $data);
        } else {
            $this->httpClient->post('/ecommerce/stores', $data);
        }

        if (!$this->httpClient->success()) {
            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
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

        $this->httpClient->put(
            sprintf(
                '/lists/%s/members/%s',
                $audience->getAudienceId(),
                MailChimp::subscriberHash($customer->getEmail())
            ),
            $data
        );

        if (!$this->httpClient->success()) {
            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasOrder(string $storeId, string $orderId): bool
    {
        $this->httpClient->get(sprintf('/ecommerce/stores/%s/orders/%s', $storeId, $orderId));

        if (!$this->httpClient->success()) {
            if (self::is404($this->httpClient->getLastResponse())) {
                return false;
            }

            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }

        return true;
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasStore(string $storeId): bool
    {
        $this->httpClient->get(sprintf('/ecommerce/stores/%s', $storeId));

        if (!$this->httpClient->success()) {
            if (self::is404($this->httpClient->getLastResponse())) {
                return false;
            }

            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }

        return true;
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function createProduct(ChannelInterface $channel, ProductInterface $product, ProductVariantInterface $productVariant = null): void
    {
        $data = $this->productDataGenerator->generate($product, $channel, $productVariant);

        $this->httpClient->post(sprintf('/ecommerce/stores/%s/products', $channel->getCode()), $data);

        if (!$this->httpClient->success()) {
            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasProduct(ChannelInterface $channel, ProductInterface $product): bool
    {
        $this->httpClient->get(sprintf('/ecommerce/stores/%s/products/%s', $channel->getCode(), $product->getCode()));

        if (!$this->httpClient->success()) {
            if (self::is404($this->httpClient->getLastResponse())) {
                return false;
            }

            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }

        return true;
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function createProductVariant(ChannelInterface $channel, ProductVariantInterface $productVariant): void
    {
        $data = $this->productVariantDataGenerator->generate($productVariant, $channel);

        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $this->httpClient->post(sprintf('/ecommerce/stores/%s/products/%s/variants', $channel->getCode(), $product->getCode()), $data);

        if (!$this->httpClient->success()) {
            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }
    }

    /**
     * @throws JsonException
     * @throws StringsException
     */
    private function hasProductVariant(ChannelInterface $channel, ProductVariantInterface $productVariant): bool
    {
        $product = $productVariant->getProduct();
        Assert::notNull($product);

        $this->httpClient->get(sprintf(
            '/ecommerce/stores/%s/products/%s/variants/%s',
            $channel->getCode(), $product->getCode(), $productVariant->getCode()
        ));

        if (!$this->httpClient->success()) {
            if (self::is404($this->httpClient->getLastResponse())) {
                return false;
            }

            throw new ClientException(self::getError($this->httpClient->getLastResponse()));
        }

        return true;
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

    /**
     * @throws JsonException
     */
    private static function getError(array $response): string
    {
        if (!isset($response['body'])) {
            throw new ClientException('No body set on response');
        }

        $body = json_decode($response['body'], true);

        $error = $body['title'] . ': ' . $body['detail'];

        if (isset($body['errors']) && is_array($body['errors'])) {
            $error .= "\n\nErrors\n------";
            foreach ($body['errors'] as $item) {
                $errorLine = '';
                if ('' !== $item['field']) {
                    $errorLine .= $item['field'] . ': ';
                }
                $errorLine .= $item['message'];

                $error .= "\n" . $errorLine;
            }
        }

        return $error;
    }

    private static function is404(array $response): bool
    {
        return isset($response['headers']['http_code']) && 404 === $response['headers']['http_code'];
    }
}
