<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use DrewM\MailChimp\MailChimp;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class MailchimpApiClient implements MailchimpApiClientInterface
{
    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    public function __construct(MailchimpConfigContextInterface $mailchimpConfigContext)
    {
        $this->mailchimpConfigContext = $mailchimpConfigContext;
    }

    /**
     * @param string $email
     * @param string $listId
     *
     * @throws MailchimpApiException
     */
    public function exportEmail(string $email, string $listId): void
    {
        try {
            $this->request()->post(sprintf('lists/%s/members', $listId), [
                'email_address' => $email,
                'status' => 'subscribed',
            ]);
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * @param string $email
     * @param string $listId
     *
     * @throws MailchimpApiException
     */
    public function removeEmail(string $email, string $listId): void
    {
        $request = $this->request();

        try {
            $request->delete(sprintf('lists/%s/members/%s',
                    $listId,
                    $request->subscriberHash($email)
                )
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function exportOrder(OrderInterface $order): void
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        try {
            $this->request()->post(sprintf('/ecommerce/stores/%s/orders',
                    $this->mailchimpConfigContext->getConfig()->getStoreId()), [
                        'id' => $order->getId(),
                        'customer' => [
                            'id' => $customer->getId(),
                            'email_address' => $customer->getEmail(),
                            'opt_in_status' => $customer->isSubscribedToNewsletter(),
                        ],
                    ]
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    public function removeOrder(OrderInterface $order): void
    {
        try {
            $this->request()->delete(sprintf('/ecommerce/stores/%s/orders/%s',
                    $this->mailchimpConfigContext->getConfig()->getStoreId(),
                    $order->getId()
                )
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

    /**
     * @return MailChimp
     *
     * @throws MailchimpApiException
     */
    private function request(): MailChimp
    {
        try {
            $config = $this->mailchimpConfigContext->getConfig();
            $mailchimpClient = new MailChimp($config->getApiKey());
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }

        return $mailchimpClient;
    }
}
