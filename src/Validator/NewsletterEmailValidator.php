<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Validator;

use Setono\SyliusMailchimpPlugin\Validator\Constraints\UniqueEmailConstraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NewsletterEmailValidator implements NewsletterEmailValidatorInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(string $email): array
    {
        $violations = $this->validator->validate($email, [
            new Email(['message' => 'setono_sylius_mailchimp_plugin.ui.invalid_email']),
            new NotBlank(['message' => 'setono_sylius_mailchimp_plugin.ui.email_not_blank']),
            new UniqueEmailConstraint(),
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
