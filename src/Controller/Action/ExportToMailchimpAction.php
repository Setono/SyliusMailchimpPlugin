<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExportToMailchimpAction
{
    /** @var CustomerNewsletterExporterInterface */
    private $customerNewsletterExporter;

    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        CustomerNewsletterExporterInterface $customerNewsletterExporter,
        MailchimpConfigContextInterface $mailchimpConfigContext,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->customerNewsletterExporter = $customerNewsletterExporter;
        $this->mailchimpConfigContext = $mailchimpConfigContext;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): Response
    {
        if (false === $request->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException();
        }

        if (false === $this->mailchimpConfigContext->isFullySetUp()) {
            $this->flashBag->add('error', $this->translator->trans('setono_sylius_mailchimp.ui.configure_first'));

            $url = $this->urlGenerator->generate('setono_sylius_mailchimp_admin_config_update', [
                'id' => $this->mailchimpConfigContext->getConfig()->getId(),
            ]);

            return new JsonResponse(['redirect' => $url], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        $export = $this->customerNewsletterExporter->exportNotExportedCustomers();

        if (null === $export) {
            $this->flashBag->add('info', $this->translator->trans('setono_sylius_mailchimp.ui.nothing_to_export'));

            return new JsonResponse(['message' => 'Nothing to export.'], Response::HTTP_OK);
        }

        if (MailchimpExportInterface::COMPLETED_STATE === $export->getState()) {
            $this->flashBag->add('success', $this->translator->trans('setono_sylius_mailchimp.ui.export_succeeded'));

            return new JsonResponse(['message' => 'Export succeeded.'], Response::HTTP_OK);
        }

        $this->flashBag->add('error', $this->translator->trans('setono_sylius_mailchimp.ui.export_failed'));

        return new JsonResponse(['message' => 'Export failed.'], Response::HTTP_EXPECTATION_FAILED);
    }
}
