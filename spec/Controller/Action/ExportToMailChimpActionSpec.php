<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailChimpPlugin\Controller\Action;

use PhpSpec\ObjectBehavior;
use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Setono\SyliusMailChimpPlugin\Controller\Action\ExportToMailChimpAction;
use Setono\SyliusMailChimpPlugin\Entity\MailChimpConfigInterface;
use Setono\SyliusMailChimpPlugin\Exporter\CustomerNewsletterExporterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ExportToMailChimpActionSpec extends ObjectBehavior
{
    function let(
        CustomerNewsletterExporterInterface $customerNewsletterExporter,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $this->beConstructedWith(
            $customerNewsletterExporter,
            $mailChimpConfigContext,
            $flashBag,
            $translator,
            $urlGenerator
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportToMailChimpAction::class);
    }

    function it_exports(
        Request $request,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpConfigInterface $mailChimpConfig
    ): void {
        $request->isXmlHttpRequest()->willReturn(true);
        $mailChimpConfigContext->isFullySetUp()->willReturn(true);
        $mailChimpConfig->getId()->willReturn(1);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);

        $response = $this->__invoke($request);

        $response->getStatusCode()->shouldBeEqualTo(Response::HTTP_OK);
    }

    function it_exports_non_authoritative_information(
        Request $request,
        MailChimpConfigContextInterface $mailChimpConfigContext,
        MailChimpConfigInterface $mailChimpConfig
    ): void {
        $request->isXmlHttpRequest()->willReturn(true);
        $mailChimpConfigContext->isFullySetUp()->willReturn(false);
        $mailChimpConfig->getId()->willReturn(1);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);

        $response = $this->__invoke($request);

        $response->getStatusCode()->shouldBeEqualTo(Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
    }
}
