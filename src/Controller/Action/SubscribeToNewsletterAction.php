<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Handler\EmailSubscriptionHandlerInterface;
use Setono\SyliusMailchimpPlugin\Validator\NewsletterEmailValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubscribeToNewsletterAction
{
    /** @var NewsletterEmailValidatorInterface */
    private $newsletterEmailValidator;

    /** @var EmailSubscriptionHandlerInterface */
    private $newsletterSubscriptionHandler;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        NewsletterEmailValidatorInterface $newsletterEmailValidator,
        CsrfTokenManagerInterface $csrfTokenManager,
        EmailSubscriptionHandlerInterface $newsletterSubscriptionHandler,
        TranslatorInterface $translator
    ) {
        $this->newsletterEmailValidator = $newsletterEmailValidator;
        $this->newsletterSubscriptionHandler = $newsletterSubscriptionHandler;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $errors = $this->newsletterEmailValidator->validate($email);
        $token = new CsrfToken('setono_newsletter_subscribe', $request->request->get('_token'));

        if (false === $this->csrfTokenManager->isTokenValid($token)) {
            $errors[] = $this->translator->trans('setono_sylius_mailchimp.ui.invalid_csrf_token');
        }

        if (0 === count($errors)) {
            $this->newsletterSubscriptionHandler->handle($email);

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => $this->translator->trans('setono_sylius_mailchimp.ui.subscribed_successfully'),
                ]
            );
        }

        return new JsonResponse(
            [
                'success' => false,
                'errors' => json_encode($errors),
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
