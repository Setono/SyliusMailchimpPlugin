<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Webmozart\Assert\Assert;

class OrderListener
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MailchimpApiClientInterface */
    private $mailChimpApiClient;

    /** @var MailchimpConfigContextInterface */
    private $mailChimpConfigContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var EntityManagerInterface */
    private $mailChimpListManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $supportedRoutes;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MailchimpApiClientInterface $mailChimpApiClient,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        EntityManagerInterface $mailChimpListManager,
        LoggerInterface $logger,
        array $supportedRoutes
    ) {
        $this->orderRepository = $orderRepository;
        $this->mailChimpApiClient = $mailChimpApiClient;
        $this->mailChimpConfigContext = $mailChimpConfigContext;
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->mailChimpListManager = $mailChimpListManager;
        $this->logger = $logger;
        $this->supportedRoutes = $supportedRoutes;
    }

    public function manageSubscription(PostResponseEvent $postResponseEvent): void
    {
        try {
            $request = $postResponseEvent->getRequest();

            if (!in_array($request->get('_route'), $this->supportedRoutes)) {
                return;
            }

            /** @var OrderInterface $order */
            $order = $request->get('order');

            Assert::notNull($order, sprintf('Order not found.'));

            /** @var CustomerInterface $customer */
            $customer = $order->getUser();

            if ($customer->isSubscribedToNewsletter()) {
                $this->subscribe($order);
            }

            $this->mailChimpListManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    private function subscribe(OrderInterface $order): void
    {
        $this->mailChimpApiClient->exportOrder($order);
    }
}
