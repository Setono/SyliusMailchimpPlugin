<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Psr\Log\LoggerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientFactoryInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Webmozart\Assert\Assert;

final class CustomerNewsletterListener
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var MailchimpListRepositoryInterface */
    private $mailchimpListRepository;

    /** @var MailchimpApiClientFactoryInterface */
    private $mailchimpApiClientFactory;

    /** @var CustomerNewsletterExporterInterface */
    private $customerNewsletterExporter;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $supportedRoutes;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        MailchimpListRepositoryInterface $mailchimpListRepository,
        MailchimpApiClientFactoryInterface $mailchimpApiClientFactory,
        CustomerNewsletterExporterInterface $customerNewsletterExporter,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        LoggerInterface $logger,
        array $supportedRoutes
    ) {
        $this->customerRepository = $customerRepository;
        $this->mailchimpListRepository = $mailchimpListRepository;
        $this->mailchimpApiClientFactory = $mailchimpApiClientFactory;
        $this->customerNewsletterExporter = $customerNewsletterExporter;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->logger = $logger;
        $this->supportedRoutes = $supportedRoutes;
    }

    public function manageSubscription(PostResponseEvent $postResponseEvent): void
    {
        $request = $postResponseEvent->getRequest();

        if (!in_array($request->get('_route'), $this->supportedRoutes)) {
            return;
        }

        // @todo Check why it done this weird way...
        $emailPieces = array_column($request->request->all(), 'email');
        $email = end($emailPieces);

        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        Assert::notNull($customer, sprintf('Customer with %s email not found.', $email));

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $mailchimpLists = $this->mailchimpListRepository->findByChannel($channel);
        foreach ($mailchimpLists as $mailchimpList) {
            try {
                if ($mailchimpList->shouldCustomerBeExported($customer)) {
                    $this->customerNewsletterExporter->exportCustomer(
                        $mailchimpList,
                        $customer
                    );
                } else {
                    try {
                        $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());
                    } catch (\Exception $e) {
                        return;
                    }

                    $apiClient->removeEmail(
                        $mailchimpList->getAudienceId(),
                        $customer->getEmailCanonical()
                    );
                }
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        $this->mailchimpListRepository->add($mailchimpList);
    }
}
