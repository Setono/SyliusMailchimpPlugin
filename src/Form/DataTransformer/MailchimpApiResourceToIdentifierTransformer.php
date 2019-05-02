<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Form\Type\DataTransformer;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClient;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class MailchimpApiResourceToIdentifierTransformer implements DataTransformerInterface
{
    /** @var MailchimpApiClient */
    private $mailchimpApiClient;

    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     */
    public function __construct(
        MailchimpApiClient $mailchimpApiClient,
        string $resource,
        ?string $identifier = null
    ) {
        $this->mailchimpApiClient = $mailchimpApiClient;
        $this->identifier = $identifier ?? 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        dump($value);

        return '';

        return $value[$this->identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        $this->mailchimpApiClient->
        $resource = $this->repository->findOneBy([$this->identifier => $value]);
        if (null === $resource) {
            throw new TransformationFailedException(sprintf(
                'Object "%s" with identifier "%s"="%s" does not exist.',
                $this->repository->getClassName(),
                $this->identifier,
                $value
            ));
        }

        return $resource;
    }
}
