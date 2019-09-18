<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderExportManager implements OrderExportManagerInterface
{
    /** @var AudienceRepositoryInterface */
    private $mailchimpListRepository;

    /** @var OrderExportDataGeneratorInterface */
    private $orderExportDataGenerator;

    /** @var MailchimpApiClientInterface */
    private $mailchimpApiClient;

    public function __construct(
        AudienceRepositoryInterface $mailchimpListRepository,
        OrderExportDataGeneratorInterface $orderExportDataGenerator,
        MailchimpApiClientInterface $mailchimpApiClient
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->orderExportDataGenerator = $orderExportDataGenerator;
        $this->mailchimpApiClient = $mailchimpApiClient;
    }

    /**
     * {@inheritdoc}
     */
    public function exportOrder(OrderInterface $order): void
    {
        $mailchimpLists = $this->mailchimpListRepository->findByChannelWithStoreConfigured($order->getChannel());
        foreach ($mailchimpLists as $mailchimpList) {
            try {
                $apiClient = $this->mailchimpApiClient;
            } catch (\Exception $e) {
                return;
            }

            $apiClient->createStore(
                $this->orderExportDataGenerator->generateStoreExportData($mailchimpList)
            );

            $productsData = $this->orderExportDataGenerator->generateOrderProductsExportData($order, $mailchimpList);
            foreach ($productsData as $productData) {
                $apiClient->exportProduct(
                    $mailchimpList->getStoreId(),
                    $productData
                );
            }

            $apiClient->exportOrder(
                $mailchimpList->getStoreId(),
                $this->orderExportDataGenerator->generateOrderExportData($order, $mailchimpList)
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @todo Remove order from mailchimp on order cancellation event?
     */
    public function removeOrder(OrderInterface $order): void
    {
        $mailchimpLists = $this->mailchimpListRepository->findByChannelWithStoreConfigured($order->getChannel());
        foreach ($mailchimpLists as $mailchimpList) {
            try {
                $apiClient = $this->mailchimpApiClient;
            } catch (\Exception $e) {
                return;
            }

            $apiClient->removeOrder(
                $mailchimpList->getStoreId(),
                $order->getId()
            );
        }
    }
}
