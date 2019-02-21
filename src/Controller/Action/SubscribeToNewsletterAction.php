<?php

/*
 * This file has been created by developers from setono.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://setono.shop and write us
 * an email on mikolaj.krol@setono.pl.
 */

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Handler\NewsletterSubscriptionHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubscribeToNewsletterAction
{
    /** @var ValidatorInterface */
    private $newsletterEmailValidator;

    /** @var NewsletterSubscriptionHandlerInterface */
    private $newsletterSubscriptionHandler;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        ValidatorInterface $newsletterEmailValidator,
        CsrfTokenManagerInterface $csrfTokenManager,
        NewsletterSubscriptionHandlerInterface $newsletterSubscriptionHandler,
        TranslatorInterface $translator
    )
    {
        $this->newsletterEmailValidator = $newsletterEmailValidator;
        $this->newsletterSubscriptionHandler = $newsletterSubscriptionHandler;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $errors = $this->newsletterEmailValidator->validate($email);
        $token = new CsrfToken('newsletter', $request->request->get('_token'));

        $this->csrfTokenManager->isTokenValid($token);

        if (false === $this->csrfTokenManager->isTokenValid($token)) {
            $errors[] = $this->translator->trans('setono_sylius_mailchimp_plugin.ui.invalid_csrf_token');
        }

        if (0 === count($errors)) {
            $this->newsletterSubscriptionHandler->subscribe($email);

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => $this->translator->trans('setono_sylius_mailchimp_plugin.ui.subscribed_successfully'),
                ]
            );
        }

        return new JsonResponse(
            [
                'success' => false,
                'errors' => json_encode($errors)
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
