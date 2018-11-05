<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\ApiClient;

use DrewM\MailChimp\MailChimp;
use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Setono\SyliusMailChimpPlugin\Exception\MailChimpApiException;

final class MailChimpApiClient implements MailChimpApiClientInterface
{
    /** @var MailChimpConfigContextInterface */
    private $mailChimpConfigContext;

    public function __construct(MailChimpConfigContextInterface $mailChimpConfigContext)
    {
        $this->mailChimpConfigContext = $mailChimpConfigContext;
    }

    public function exportEmail(string $email, string $listId): void
    {
        try {
            $this->request()->post(sprintf('lists/%s/members', $listId), [
                'email_address' => $email,
                'status' => 'subscribed',
            ]);
        } catch (\Exception $exception) {
            throw new MailChimpApiException($exception->getMessage());
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
            throw new MailChimpApiException($exception->getMessage());
        }
    }

    private function request(): MailChimp
    {
        try {
            $config = $this->mailChimpConfigContext->getConfig();
            $mailChimpClient = new MailChimp($config->getApiKey());
        } catch (\Exception $exception) {
            throw new MailChimpApiException($exception->getMessage());
        }

        return $mailChimpClient;
    }
}
