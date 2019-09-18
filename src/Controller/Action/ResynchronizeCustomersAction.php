<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\ResynchronizeCustomers;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This action will reset the last mailchimp sync property on customers
 * and thereafter run the synchronization of customers
 */
final class ResynchronizeCustomersAction
{
    /**
     * @var MessageBusInterface
     */
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
        $this->commandBus->dispatch(new ResynchronizeCustomers());

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_mailchimp_admin_audience_index'));
    }
}
