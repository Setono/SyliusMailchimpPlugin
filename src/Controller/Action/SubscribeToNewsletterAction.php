<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Form\Type\SubscribeToNewsletterType;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class SubscribeToNewsletterAction
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var Environment */
    private $twig;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    /** @var ClientInterface */
    private $client;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twig,
        TranslatorInterface $translator,
        ChannelContextInterface $channelContext,
        AudienceRepositoryInterface $audienceRepository,
        ClientInterface $client
    ) {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->channelContext = $channelContext;
        $this->audienceRepository = $audienceRepository;
        $this->client = $client;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(Request $request): Response
    {
        $audience = $this->getAudience();

        $form = $this->formFactory->create(SubscribeToNewsletterType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (null === $audience) {
                return $this->json(
                    $this->translator->trans('setono_sylius_mailchimp.ui.no_audience_associated_with_channel'),
                    400
                );
            }

            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);
                if (is_string($errors)) {
                    $errors = [$errors];
                }

                return $this->json(
                    $this->translator->trans('setono_sylius_mailchimp.ui.an_error_occurred'), 400, $errors
                );
            }

            $this->client->subscribeEmail($audience, $form->get('email')->getData());

            return $this->json($this->translator->trans('setono_sylius_mailchimp.ui.subscribed_successfully'));
        }

        $content = $this->twig->render('@SetonoSyliusMailchimpPlugin/Shop/Subscribe/content.html.twig', [
            'form' => null === $audience ? null : $form->createView(),
        ]);

        return new Response($content);
    }

    private function json(string $message, int $status = 200, array $errors = []): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Taken from https://symfonycasts.com/screencast/javascript/post-proper-api-endpoint#codeblock-99cf6afd45
     *
     * @return array|string
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        /** @var FormError $error */
        foreach ($form->getErrors() as $error) {
            // only supporting 1 error per field
            // and not supporting a "field" with errors, that has more
            // fields with errors below it
            return $error->getMessage();
        }

        $errors = [];
        foreach ($form->all() as $childForm) {
            $childError = $this->getErrorsFromForm($childForm);
            if (is_string($childError)) {
                $errors[$childForm->getName()] = $childError;
            }
        }

        return $errors;
    }

    private function getAudience(): ?AudienceInterface
    {
        $channel = $this->channelContext->getChannel();

        return $this->audienceRepository->findOneByChannel($channel);
    }
}
