<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailChimpPlugin\Behat\Page\Admin\ManageConfig;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function fillField(string $field, string $value): void
    {
        $this->getDocument()->fillField($field, $value);
    }
}
