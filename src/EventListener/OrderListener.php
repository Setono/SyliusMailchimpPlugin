<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Psr\Log\LoggerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
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

    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $supportedRoutes;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MailchimpApiClientInterface $mailChimpApiClient,
        LoggerInterface $logger,
        array $supportedRoutes
    ) {
        $this->orderRepository = $orderRepository;
        $this->mailChimpApiClient = $mailChimpApiClient;
        $this->logger = $logger;
        $this->supportedRoutes = $supportedRoutes;
    }

    public function manageOrderSubscription(PostResponseEvent $postResponseEvent): void
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
                $this->mailChimpApiClient->exportOrder($order);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
