<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Page\Shop\Account;

use Sylius\Behat\Page\Shop\Account\ProfileUpdatePageInterface as BaseProfileUpdatePageInterface;

interface ProfileUpdatePageInterface extends BaseProfileUpdatePageInterface
{
    public function unSubscribeToTheNewsletter(): void;
}
