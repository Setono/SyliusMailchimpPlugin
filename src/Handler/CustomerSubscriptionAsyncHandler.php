<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

class CustomerSubscriptionAsyncHandler extends AsyncHandler
{
    public static function getEventName(): string
    {
        return 'setono_sylius_mailchimp_customer_subscription';
    }
}
