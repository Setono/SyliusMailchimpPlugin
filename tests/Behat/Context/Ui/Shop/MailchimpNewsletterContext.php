<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Entity\MailchimpListInterface;
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterPageInterface;
use Sylius\Behat\Page\Shop\Account\VerificationPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\Setono\SyliusMailchimpPlugin\Behat\Page\Shop\Account\ProfileUpdatePageInterface;
use Webmozart\Assert\Assert;

final class MailchimpNewsletterContext implements Context
{
    /** @var RegisterPageInterface */
    private $registerPage;

    /** @var RegisterElementInterface */
    private $registerElement;

    /** @var VerificationPageInterface */
    private $verificationPage;

    /** @var LoginPageInterface */
    private $loginPage;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var ProfileUpdatePageInterface */
    private $profileUpdatePage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var FactoryInterface */
    private $orderFactory;

    /** @var ChannelRepositoryInterface */
    private $channel;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var CurrencyContextInterface */
    private $currencyContext;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    /** @var EntityManagerInterface */
    private $mailchimpConfigManager;

    public function __construct(
        RegisterPageInterface $registerPage,
        RegisterElementInterface $registerElement,
        VerificationPageInterface $verificationPage,
        LoginPageInterface $loginPage,
        UserRepositoryInterface $userRepository,
        ProfileUpdatePageInterface $profileUpdatePage,
        NotificationCheckerInterface $notificationChecker,
        CurrentPageResolverInterface $currentPageResolver,
        FactoryInterface $orderFactory,
        ChannelRepositoryInterface $channel,
        LocaleContextInterface $localeContext,
        CurrencyContextInterface $currencyContext,
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        MailchimpConfigContextInterface $mailchimpConfigContext,
        EntityManagerInterface $mailchimpConfigManager
    ) {
        $this->registerPage = $registerPage;
        $this->registerElement = $registerElement;
        $this->verificationPage = $verificationPage;
        $this->loginPage = $loginPage;
        $this->userRepository = $userRepository;
        $this->profileUpdatePage = $profileUpdatePage;
        $this->notificationChecker = $notificationChecker;
        $this->currentPageResolver = $currentPageResolver;
        $this->orderFactory = $orderFactory;
        $this->channel = $channel;
        $this->localeContext = $localeContext;
        $this->currencyContext = $currencyContext;
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->mailchimpConfigContext = $mailchimpConfigContext;
        $this->mailchimpConfigManager = $mailchimpConfigManager;
    }

    /**
     * @When I have proceeded the order as a customer with :email email
     */
    public function iHaveProceededTheOrderAsACustomerWithEmail(string $email): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        /** @var CustomerInterface $customer */
        $customer = $this->userRepository->findOneByEmail($email);

        $order->setChannel(current($this->channel->findAll()));
        $order->setLocaleCode($this->localeContext->getLocaleCode());
        $order->setCurrencyCode($this->currencyContext->getCurrencyCode());
        $order->setCustomer($customer);

        $this->orderRepository->add($order);
    }

    /**
     * @Then :email email should be exported to Mailchimp
     * @Then the email :email should be exported to Mailchimp's default list
     * @Then the email :email should be exported to the Mailchimp's default list
     */
    public function emailShouldBeExportedToMailchimp(string $email): void
    {
        $config = $this->mailchimpConfigContext->getConfig();

        $this->mailchimpConfigManager->refresh($config);

        /** @var MailchimpListInterface $list */
        foreach ($config->getLists() as $list) {
            if ($list->hasEmail($email)) {
                return;
            }
        }

        throw new \LogicException();
    }

    /**
     * @When I want to modify my profile
     */
    public function iWantToModifyMyProfile(): void
    {
        $this->profileUpdatePage->open();
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->profileUpdatePage->saveChanges();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
    }

    /**
     * @When I unsubscribe from the newsletter
     */
    public function iUnsubscribeFromTheNewsletter(): void
    {
        $this->profileUpdatePage->unSubscribeToTheNewsletter();
    }

    /**
     * @Then the email :email should be removed from Mailchimp's default list
     */
    public function theEmailShouldBeRemovedFromMailchimpsDefaultList(string $email): void
    {
        $config = $this->mailchimpConfigContext->getConfig();

        $this->mailchimpConfigManager->refresh($config);

        /** @var MailchimpListInterface $list */
        foreach ($config->getLists() as $list) {
            Assert::false($list->hasEmail($email));
        }
    }
}
