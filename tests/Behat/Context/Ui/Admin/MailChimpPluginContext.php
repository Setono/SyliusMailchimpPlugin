<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailChimpPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Setono\SyliusMailChimpPlugin\Repository\MailChimpConfigRepositoryInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\DashboardPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Setono\SyliusMailChimpPlugin\Behat\Page\Admin\ManageConfig\CreatePageInterface;
use Tests\Setono\SyliusMailChimpPlugin\Behat\Page\Admin\ManageConfig\IndexPageInterface;
use Tests\Setono\SyliusMailChimpPlugin\Behat\Page\Admin\ManageConfig\UpdatePageInterface;
use WebMozart\Assert\Assert;

final class MailChimpPluginContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var CreatePageInterface */
    private $createPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var DashboardPageInterface */
    private $dashboardPage;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var MailChimpConfigRepositoryInterface */
    private $configRepository;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        DashboardPageInterface $dashboardPage,
        SharedStorageInterface $sharedStorage,
        NotificationCheckerInterface $notificationChecker,
        MailChimpConfigRepositoryInterface $configRepository,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->dashboardPage = $dashboardPage;
        $this->sharedStorage = $sharedStorage;
        $this->notificationChecker = $notificationChecker;
        $this->configRepository = $configRepository;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @When I go to the admin dashboard
     */
    public function iGoToTheAdminDashboard(): void
    {
        $this->dashboardPage->open();
    }

    /**
     * @When I click the MailChimp in configuration menu
     */
    public function iClickTheMailchimpInConfigurationMenu(): void
    {
        $this->updatePage->open(['id' => $this->configRepository->findConfig()->getId()]);
    }

    /**
     * @Then I should be on the MailChimp config create page
     * @Then I should be on the MailChimp config update page
     */
    public function iShouldBeOnTheMailchimpConfigCreatePage(): void
    {
        Assert::true($this->updatePage->isOpen(['id' => $this->configRepository->findConfig()->getId()]));
    }

    /**
     * @When I go to the MailChimp update page
     */
    public function iGoToTheMailchimpUpdatePage(): void
    {
        $this->updatePage->open(['id' => $this->configRepository->findConfig()->getId()]);
    }

    /**
     * @When I add a list
     */
    public function iAddAList(): void
    {
        $this->resolveCurrentPage()->clickAddList();
    }

    /**
     * @When I fill list code with :code
     */
    public function iFillListCodeWith(string $code): void
    {
        $this->resolveCurrentPage()->fillCode($code);
    }

    /**
     * @When I fill the list ID with :listId
     */
    public function iFillTheListIdWith(string $listId): void
    {
        $this->resolveCurrentPage()->fillId($listId);
    }

    /**
     * @When I update it
     */
    public function iUpdateIt(): void
    {
        $this->resolveCurrentPage()->saveChanges();
    }

    /**
     * @Then I should be notified that the MailChimp config has been updated
     */
    public function iShouldBeNotifiedThatTheMailchimpConfigHasBeenUpdated(): void
    {
        $this->notificationChecker->checkNotification(
            'Config has been successfully updated.',
            NotificationType::success()
        );
    }

    /**
     * @Then the MailChimp config should have one list with :code code and :listId list ID
     */
    public function theMailchimpConfigShouldHaveOneListWithCodeAndListId(string $code, string $listId): void
    {
        Assert::true($this->resolveCurrentPage()->containsList($code, $listId));
    }

    /**
     * @When I remove the last list
     */
    public function iRemoveTheLastList(): void
    {
        $this->resolveCurrentPage()->removeLastList();
    }

    /**
     * @Then the MailChimp config should have only :quantity lists
     */
    public function theMailchimpConfigShouldHaveOnlyLists(int $quantity): void
    {
        Assert::eq($this->resolveCurrentPage()->countLists(), $quantity);
    }

    /**
     * @return IndexPageInterface|CreatePageInterface|UpdatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->indexPage,
            $this->createPage,
            $this->updatePage,
        ]);
    }
}
