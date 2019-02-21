<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueNewsletterEmail extends Constraint
{
    public $message = 'setono_sylius_mailchimp_plugin.ui.unique_email';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}
