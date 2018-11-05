@managing_mailchimp_config
Feature: Managing MailChimp config
    In order to use MailChimp in several places in my web store
    As an Administrator
    I want to be able to manage MailChimp API config

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new MailChimp config
        When I go to the admin dashboard
        And I click the MailChimp in configuration menu
        Then I should be on the MailChimp config create page

    @ui
    Scenario: Updating existing MailChimp config
        Given there is already an existing MailChimp config for the store
        When I go to the admin dashboard
        And I click the MailChimp in configuration menu
        Then I should be on the MailChimp config update page

    @ui @javascript
    Scenario: Adding lists to the MailChimp config
        Given there is already an existing MailChimp config for the store
        When I go to the MailChimp update page
        And I add a list
        And I fill the list ID with "123456789"
        And I update it
        Then I should be notified that the MailChimp config has been updated
        And the MailChimp config should have one list with "homepage" code and "123456789" list ID

    @ui @javascript
    Scenario: Removing MailChimp config list
        Given there is already an existing MailChimp config for the store
        And this config has 3 lists associated to it
        When I go to the MailChimp update page
        And I remove the last list
        And I update it
        Then I should be notified that the MailChimp config has been updated
        And the MailChimp config should have only 2 lists
