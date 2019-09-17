<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class ExportRestartAction
{
    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        MailchimpExportRepositoryInterface $mailchimpExportRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailchimpExportRepository = $mailchimpExportRepository;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @todo Better use state machines for state?
     */
    public function __invoke(Request $request): Response
    {
        /** @var MailchimpExportInterface $mailchimpExport */
        $mailchimpExport = $this->mailchimpExportRepository->find(
            $request->attributes->get('id')
        );
        Assert::notNull($mailchimpExport);

        $mailchimpExport->setState(MailchimpExportInterface::RESTARTING_STATE);
        $mailchimpExport->clearErrors();
        $this->mailchimpExportRepository->add($mailchimpExport);

        $this->flashBag->add('info', $this->translator->trans('setono_sylius_mailchimp.ui.export_restarted'));

        /** @var MailchimpListInterface $mailchimpList */
        $mailchimpList = $mailchimpExport->getList();

        $defaultRedirect = $this->urlGenerator->generate('setono_sylius_mailchimp_admin_list_export_index', [
            'mailchimpListId' => $mailchimpList->getId(),
        ]);

        return new RedirectResponse(
            $request->headers->get('referer', $defaultRedirect)
        );
    }
}
