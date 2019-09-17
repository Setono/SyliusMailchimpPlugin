<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientFactoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderExportManager implements OrderExportManagerInterface
{
    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var OrderExportDataGeneratorInterface */
    private $orderExportDataGenerator;

    /** @var MailchimpApiClientFactoryInterface */
    private $mailchimpApiClientFactory;

    public function __construct(
        MailchimpListRepositoryInterface $mailchimpListRepository,
        OrderExportDataGeneratorInterface $orderExportDataGenerator,
        MailchimpApiClientFactoryInterface $mailchimpApiClientFactory
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->orderExportDataGenerator = $orderExportDataGenerator;
        $this->mailchimpApiClientFactory = $mailchimpApiClientFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function exportOrder(OrderInterface $order): void
    {
        $mailchimpLists = $this->mailchimpListRepository->findByChannelWithStoreConfigured($order->getChannel());
        foreach ($mailchimpLists as $mailchimpList) {
            /** @var MailchimpConfigInterface $mailchimpConfig */
            $mailchimpConfig = $mailchimpList->getConfig();

            try {
                $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpConfig);
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
            /** @var MailchimpConfigInterface $mailchimpConfig */
            $mailchimpConfig = $mailchimpList->getConfig();

            try {
                $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpConfig);
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
