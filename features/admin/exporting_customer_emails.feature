@Mailchimp_export
Feature: Exporting customers to Mailchimp
    In order to provide a great newsletter experience through Mailchimp automation tools
    As an Administrator
    I want to be able to export customers to Mailchimp API

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Being redirected if the Mailchimp config is not set up in admin GUI
        When I go to the Mailchimp export page
        And I click the export button
        Then I should be redirected to the Mailchimp config page
        And I should be notified that I need to set up the Mailchimp config first

    @ui @javascript
    Scenario: Exporting customers to Mailchimp via admin GUI
        Given I have a Mailchimp config set up
        And the store allows all emails to be exported
        And I have 10 customers in my database
        When I go to the Mailchimp export page
        And I click the export button
        And I refresh the page
        Then I should see that the export has a "Completed" state
        And 10 emails have been exported

    @cli
    Scenario: Seeing an exception in CLI once the Mailchimp config is not set up in CLI
        When I execute the Mailchimp export command
        Then I should see an error saying that I need to set up the Mailchimp config first

    @cli
    Scenario: Exporting customers to Mailchimp via CLI
        Given I have a Mailchimp config set up
        And I allow all emails to be exported
        And I have 15 customers in my database
        When I execute the Mailchimp export command
        Then the export should have "Completed" state
        And 15 emails should be exported for it
