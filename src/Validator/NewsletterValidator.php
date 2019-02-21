<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\Validator\Constraints\UniqueNewsletterEmail;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NewsletterValidator
{
    /** @var ValidatorInterface */
    private $validator;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    public function validate(string $email): array
    {
        $violations = $this->validator->validate($email, [
            new Email(['message' => 'setono_sylius_mailchimp_plugin.ui.invalid_email']),
            new NotBlank(['message' => 'setono_sylius_mailchimp_plugin.ui.email_not_blank']),
            new UniqueNewsletterEmail(),
        ]);

        $errors = [];

        if (0 === count($violations)) {
            return $errors;
        }

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}
