<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Loader\AudiencesLoaderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This action will load audiences from the Mailchimp API
 * and create/update entities in the Sylius shop
 */
final class LoadAudiencesAction
{
    /** @var AudiencesLoaderInterface */
    private $audiencesLoader;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        AudiencesLoaderInterface $audiencesLoader,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->audiencesLoader = $audiencesLoader;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->audiencesLoader->load(true);

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_mailchimp_admin_audience_index'));
    }
}
