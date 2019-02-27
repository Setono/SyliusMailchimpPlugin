<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueEmailConstraint extends Constraint
{
    public $message = 'setono_sylius_mailchimp.ui.unique_email';

    public function validatedBy(): string
    {
        return get_class($this) . 'Validator';
    }
}
