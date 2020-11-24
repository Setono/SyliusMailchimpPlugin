<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use function Safe\sprintf;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Repository\AudienceRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

class AudiencesLoader implements AudiencesLoaderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var ClientInterface */
    private $client;

    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    /** @var FactoryInterface */
    private $audienceFactory;

    /** @var EntityManagerInterface */
    private $audienceManager;

    public function __construct(
        ClientInterface $client,
        AudienceRepositoryInterface $audienceRepository,
        FactoryInterface $audienceFactory,
        EntityManagerInterface $audienceManager
    ) {
        $this->client = $client;
        $this->audienceRepository = $audienceRepository;
        $this->audienceFactory = $audienceFactory;
        $this->audienceManager = $audienceManager;
        $this->logger = new NullLogger();
    }

    public function load(bool $preserve = false): void
    {
        $audienceIds = $this->createOrUpdateAudiences();
        $this->audienceManager->flush();

        if ($preserve) {
            return;
        }

        /** @var AudienceInterface[] $audiencesToRemove */
        $audiencesToRemove = array_filter($this->audienceRepository->findAll(), function (AudienceInterface $audience) use ($audienceIds): bool {
            return !in_array($audience->getAudienceId(), $audienceIds, true);
        });

        foreach ($audiencesToRemove as $audienceToRemove) {
            // @todo Dispatch event to prevent removing?
            $this->audienceRepository->remove($audienceToRemove);

            $this->logger->info(sprintf(
                'Audience %s was removed.',
                $audienceToRemove->getAudienceId()
            ));
        }
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

            $this->audienceManager->persist($audience);

            $this->logger->info(sprintf(
                'Audience %s was %s.',
                $audienceId,
                $audience->getId() !== null ? 'updated' : 'added'
            ));

            return $audienceId;
        }, $mailchimpAudiences);
    }
}
