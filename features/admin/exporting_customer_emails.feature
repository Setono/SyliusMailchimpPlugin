@mailchimp_export
Feature: Exporting customers to MailChimp
    In order to provide a great newsletter experience through MailChimp automation tools
    As an Administrator
    I want to be able to export customers to MailChimp API

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Being redirected if the MailChimp config is not set up in admin GUI
        When I go to the MailChimp export page
        And I click the export button
        Then I should be redirected to the MailChimp config page
        And I should be notified that I need to set up the MaiLChimp config first

    @ui @javascript
    Scenario: Exporting customers to MailChimp via admin GUI
        Given I have a MailChimp config set up
        And the store allows all emails to be exported
        And I have 10 customers in my database
        When I go to the MailChimp export page
        And I click the export button
        And I refresh the page
        Then I should see that the export has a "Completed" state
        And 10 emails have been exported

    @cli
    Scenario: Seeing an exception in CLI once the MailChimp config is not set up in CLI
        When I execute the MailChimp export command
        Then I should see an error saying that I need to set up the MaiLChimp config first

    @cli
    Scenario: Exporting customers to MailChimp via CLI
        Given I have a MailChimp config set up
        And I allow all emails to be exported
        And I have 15 customers in my database
        When I execute the MailChimp export command
        Then the export should have "Completed" state
        And 15 emails should be exported for it
