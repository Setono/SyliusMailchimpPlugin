<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Validator\Constraints;

use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueEmailConstraintValidator extends ConstraintValidator
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function validate($email, Constraint $constraint): void
    {
        if (!$this->isEmailValid($email)) {
            /** @var UniqueEmailConstraint $constraint */
            $this->context->addViolation($constraint->message);
        }
    }

    private function isEmailValid(string $email): bool
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        if (!$customer instanceof CustomerInterface) {
            return true;
        }

        if ($customer->isSubscribedToNewsletter()) {
            return false;
        }

        return true;
    }
}
