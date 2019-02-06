<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Page\Admin\ManageConfig;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function fillCode(string $code): void;

    public function fillId(string $id): void;

    public function containsList(string $id): bool;

    public function removeLastList(): void;

    public function clickAddList(): void;

    public function countLists(): int;

    public function waitForRedirect(): bool;
}
