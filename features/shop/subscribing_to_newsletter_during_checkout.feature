@Mailchimp_subscription_during_checkout
Feature: Subscribing to the newsletter during the checkout
    In order to be up to date with products and promotions
    As a customer
    I want my email to be exported to Mailchimp once the system allows to do so

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying offline
        And the Mailchimp config is set up
        And the store allows all emails to be exported
        And there is a customer account "kimi@raikkonen.fi" identified by "sylius"
        And I am logged in as "kimi@raikkonen.fi"

    @ui
    Scenario: Exporting an email during the checkout
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        When I confirm my order
        Then "kimi@raikkonen.fi" email should be exported to Mailchimp
