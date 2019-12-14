<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Loader;

use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

class AudiencesLoader implements AudiencesLoaderInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    /** @var FactoryInterface */
    private $audienceFactory;

    public function __construct(
        ClientInterface $client,
        AudienceRepositoryInterface $audienceRepository,
        FactoryInterface $audienceFactory
    ) {
        $this->client = $client;
        $this->audienceRepository = $audienceRepository;
        $this->audienceFactory = $audienceFactory;
    }

    public function load(bool $preserve = false): void
    {
        $audienceIds = $this->createOrUpdateAudiences();

        if ($preserve) {
            return;
        }

        $this->audienceRepository->removeAllExceptAudienceIds($audienceIds);
    }

    /**
     * @return string[] AudienceIDs of created or updated audiences
     */
    protected function createOrUpdateAudiences(): array
    {
        $mailchimpAudiences = $this->client->getAudiences([
            'fields' => ['id', 'name'],
        ]);

        return array_map(function (array $mailchimpAudience): string {
            /** @var string|null $audienceId */
            $audienceId = $mailchimpAudience['id'];
            Assert::notNull($audienceId);

            $audience = $this->audienceRepository->findOneByAudienceId($audienceId);
            if (null === $audience) {
                /** @var AudienceInterface $audience */
                $audience = $this->audienceFactory->createNew();
                $audience->setAudienceId($audienceId);
            }

            $audience->setName($mailchimpAudience['name']);

            $this->audienceRepository->add($audience);

            return $audienceId;
        }, $mailchimpAudiences);
    }
}
