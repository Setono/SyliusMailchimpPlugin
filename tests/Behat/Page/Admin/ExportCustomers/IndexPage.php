<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Page\Admin\ExportCustomers;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function clickExport(): void
    {
        $this->getDocument()->findById('setono-mailchimp-export')->click();
    }

    public function getState($state): string
    {
        return $this->getDocument()
            ->find('css', '#content > div.ui.segment.overflow-x-auto > table > tbody > tr:nth-child(1) > td:nth-child(1) > div')
            ->getText()
        ;
    }
}
