@mailchimp_shop_newsletter
Feature: Subscribing to the newsletter during registration
    In order to be up to date with products and promotions
    As a Visitor
    I want to subscribe myself to the MailChimp newsletter during account registration

    Background:
        Given the store operates on a single channel in "United States"
        And the MailChimp config is set up

    @ui
    Scenario: Subscribing to the newsletter during registration
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I subscribe to the newsletter
        And I register this account
        And I verify my account using link sent to "goodman@gmail.com"
        And I log in as "goodman@gmail.com" with "heisenberg" password
        Then I should be subscribed to the newsletter
        And the email "goodman@gmail.com" should be exported to the MailChimp's default list
