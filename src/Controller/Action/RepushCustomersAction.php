<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Message\Command\RepushCustomers;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This action will call the RepushCustomers command which resets
 * the 'pushed to mailchimp' property on customers and thereafter run the push of all customers
 */
final class RepushCustomersAction
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        MessageBusInterface $commandBus,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->commandBus = $commandBus;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->commandBus->dispatch(new RepushCustomers());

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_mailchimp_admin_audience_index'));
    }
}
