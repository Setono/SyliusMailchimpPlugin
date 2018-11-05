<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailChimpPlugin\Behat\Page\Shop\Account;

use Sylius\Behat\Page\Shop\Account\ProfileUpdatePage as BaseProfileUpdatePage;

class ProfileUpdatePage extends BaseProfileUpdatePage implements ProfileUpdatePageInterface
{
    public function unSubscribeToTheNewsletter(): void
    {
        $this->getDocument()->uncheckField('Subscribe to the newsletter');
    }
}
