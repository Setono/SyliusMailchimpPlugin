<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpConfigRepositoryInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationChecker;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Setono\SyliusMailchimpPlugin\Behat\Page\Admin\ExportCustomers\IndexPageInterface;
use Tests\Setono\SyliusMailchimpPlugin\Behat\Page\Admin\ManageConfig\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ExportCustomersContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var NotificationChecker */
    private $notificationChecker;

    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var MailchimpConfigRepositoryInterface */
    private $configRepository;

    public function __construct(
        IndexPageInterface $indexPage,
        NotificationChecker $notificationChecker,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        MailchimpConfigRepositoryInterface $configRepository
    ) {
        $this->indexPage = $indexPage;
        $this->notificationChecker = $notificationChecker;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->configRepository = $configRepository;
    }

    /**
     * @When I go to the Mailchimp export page
     */
    public function iGoToTheMailchimpExportPage(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I click the export button
     */
    public function iClickTheExportButton(): void
    {
        $this->resolveCurrentPage()->clickExport();
    }

    /**
     * @Then I should be redirected to the Mailchimp config page
     */
    public function iShouldBeRedirectedToTheMailchimpConfigPage(): void
    {
        $this->updatePage->waitForRedirect();
        Assert::true($this->updatePage->isOpen(['id' => $this->configRepository->findConfig()->getId()]));
    }

    /**
     * @Then I should be notified that I need to set up the Mailchimp config first
     */
    public function iShouldBeNotifiedThatINeedToSetUpTheMailchimpConfigFirst(): void
    {
        $this->notificationChecker->checkNotification(
            'Please set up the Mailchimp config properly first.',
            NotificationType::failure()
        );
    }

    /**
     * @When I refresh the page
     * @When I refresh the page again
     */
    public function iRefreshThePage(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see that a new export with :state state
     * @Then I should see that the export has a :state state
     */
    public function iShouldSeeThatANewExportWithState(string $state): void
    {
        Assert::eq($this->resolveCurrentPage()->getState($state), $state);
    }

    /**
     * @Then :count emails have been exported
     */
    public function emailsHaveBeenExported(int $count): void
    {
        Assert::eq(current($this->indexPage->getColumnFields('emails_count')), $count);
    }

    /**
     * @return IndexPageInterface|UpdatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->indexPage,
            $this->updatePage,
        ]);
    }
}
