<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Context\LocaleContextInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Webmozart\Assert\Assert;

final class CustomerNewsletterListener
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

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
        CustomerRepositoryInterface $customerRepository,
        MailchimpApiClientInterface $mailChimpApiClient,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        EntityManagerInterface $mailChimpListManager,
        LoggerInterface $logger,
        array $supportedRoutes
    ) {
        $this->customerRepository = $customerRepository;
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

            $emailPieces = array_column($request->request->all(), 'email');
            $email = end($emailPieces);
            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository->findOneBy(['email' => $email]);

            if (null === $customer) {
                return;
            }

            if ($customer->isSubscribedToNewsletter()) {
                $this->subscribe($customer);
            }

            $this->mailChimpListManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    private function subscribe(CustomerInterface $customer): void
    {
        $list = $this->getList();
        $email = $customer->getEmail();

        $this->mailChimpApiClient->exportEmail($email, $list->getListId());

        $list->addEmail($email);
    }

    private function getList(): MailchimpListInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $this->localeContext->getLocale();
        /** @var MailchimpConfigInterface $config */
        $config = $this->mailChimpConfigContext->getConfig();
        $list = $config->getListForChannelAndLocale($channel, $locale);

        Assert::notNull($list);

        return $list;
    }
}
