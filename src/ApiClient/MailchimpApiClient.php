<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\ApiClient;

use DrewM\MailChimp\MailChimp;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Exception\MailchimpApiException;

final class MailchimpApiClient implements MailchimpApiClientInterface
{
    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    public function __construct(MailchimpConfigContextInterface $mailchimpConfigContext)
    {
        $this->mailchimpConfigContext = $mailchimpConfigContext;
    }

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

    public function removeEmail(string $email, string $listId): void
    {
        $request = $this->request();

        try {
            $request->delete(sprintf(
                'lists/%s/members/%s',
                $listId,
                    $request->subscriberHash($email))
            );
        } catch (\Exception $exception) {
            throw new MailchimpApiException($exception->getMessage());
        }
    }

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
