<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Controller\Action;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This action will load audiences from the Mailchimp API
 * and create/update entities in the Sylius shop
 */
final class LoadAudiencesAction
{
    /** @var MailchimpApiClientInterface */
    private $client;

    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    /** @var FactoryInterface */
    private $audienceFactory;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        MailchimpApiClientInterface $client,
        AudienceRepositoryInterface $audienceRepository,
        FactoryInterface $audienceFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->client = $client;
        $this->audienceRepository = $audienceRepository;
        $this->audienceFactory = $audienceFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var AudienceInterface[] $objs */
        $objs = $this->audienceRepository->findAll();

        $audiences = $this->client->getAudiences([
            'fields' => ['id', 'name'],
        ]);

        // array of audience ids that should not be removed
        $doNotRemove = [];

        foreach ($audiences as $audience) {
            $entity = null;
            foreach ($objs as $obj) {
                if ($obj->getAudienceId() === $audience['id']) {
                    $entity = $obj;
                    $doNotRemove[] = $obj->getAudienceId();
                }
            }

            if (null === $entity) {
                /** @var AudienceInterface $entity */
                $entity = $this->audienceFactory->createNew();
                $entity->setAudienceId($audience['id']);
            }

            $entity->setName($audience['name']);

            $this->audienceRepository->add($entity);
        }

        foreach ($objs as $obj) {
            if (in_array($obj->getAudienceId(), $doNotRemove, true)) {
                continue;
            }

            $this->audienceRepository->remove($obj);
        }

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_mailchimp_admin_audience_index'));
    }
}
