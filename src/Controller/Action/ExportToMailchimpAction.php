<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
        if (false === $this->mailchimpConfigContext->isFullySetUp()) {
            $this->flashBag->add('error', $this->translator->trans('setono_sylius_mailchimp.ui.configure_first'));

            $url = $this->urlGenerator->generate('setono_sylius_mailchimp_admin_config_update', [
                'id' => $this->mailchimpConfigContext->getConfig()->getId(),
            ]);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['redirect' => $url], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
            } else {
                return new RedirectResponse($url);
            }
        }

        // @todo Move to queue
        $export = $this->customerNewsletterExporter->exportNotExportedCustomers();

        if (null === $export) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['message' => 'Nothing to export.'], Response::HTTP_OK);
            }
            $this->flashBag->add('info', $this->translator->trans('setono_sylius_mailchimp.ui.nothing_to_export'));
        } elseif (MailchimpExportInterface::COMPLETED_STATE === $export->getState()) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['message' => 'Export succeeded.'], Response::HTTP_OK);
            }
            $this->flashBag->add('success', $this->translator->trans('setono_sylius_mailchimp.ui.export_succeeded'));
        } else {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['message' => 'Export failed.'], Response::HTTP_EXPECTATION_FAILED);
            }
            $this->flashBag->add('error', $this->translator->trans('setono_sylius_mailchimp.ui.export_failed'));
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('setono_sylius_mailchimp_admin_export_index')
        );
    }
}
