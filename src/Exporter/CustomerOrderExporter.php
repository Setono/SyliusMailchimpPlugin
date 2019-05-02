<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientFactoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerOrderExporter implements CustomerOrderExporterInterface
{
    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var ExportDataGeneratorInterface */
    private $exportDataGenerator;

    /** @var MailchimpApiClientFactoryInterface */
    private $mailchimpApiClientFactory;

    public function __construct(
        MailchimpListRepositoryInterface $mailchimpListRepository,
        ExportDataGeneratorInterface $exportDataGenerator,
        MailchimpApiClientFactoryInterface $mailchimpApiClientFactory
    ) {
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->exportDataGenerator = $exportDataGenerator;
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
                $this->exportDataGenerator->generateStoreExportData($mailchimpList)
            );

            $productsData = $this->exportDataGenerator->generateOrderProductsExportData($order, $mailchimpList);
            foreach ($productsData as $productData) {
                $apiClient->exportProduct(
                    $mailchimpList->getStoreId(),
                    $productData
                );
            }

            $apiClient->exportOrder(
                $mailchimpList->getStoreId(),
                $this->exportDataGenerator->generateOrderExportData($order, $mailchimpList)
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
