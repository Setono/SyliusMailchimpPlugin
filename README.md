<h1 align="center">
    <a href="https://packagist.org/packages/setono/sylius-mailchimp-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/setono/sylius-mailchimp-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/setono/sylius-mailchimp-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/setono/sylius-mailchimp-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/Setono/SyliusMailChimpPlugin" title="Build status" target="_blank">
            <img src="https://img.shields.io/travis/Setono/SyliusMailChimpPlugin/master.svg" />
        </a>
    <a href="https://scrutinizer-ci.com/g/Setono/SyliusMailChimpPlugin" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/Setono/SyliusMailChimpPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/setono/sylius-mailchimp-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/setono/sylius-mailchimp-plugin/downloads" />
    </a>
</h1>

## Installation

1. Require plugin with composer:

    ```bash
    composer require setono/mailchimp-plugin
    ```

2. Import configuration:

    ```yaml
    imports:
        - { resource: "@SetonoSyliusMailChimpPlugin/Resources/config/config.yml" }
    ```
3. Import routing:
   
    ```yaml
    setono_sylius_mailchimp_plugin:
        resource: "@SetonoSyliusMailChimpPlugin/Resources/config/routing.yml"
    ```

4. Add plugin class to your `AppKernel`:

    ```php
    $bundles = [
        new \Setono\SyliusMailChimpPlugin\SetonoSyliusMailChimpPlugin(),
    ];
    ```
5. Update your database:

    ```bash
    $ bin/console doctrine:migrations:diff
    $ bin/console doctrine:migrations:migrate
    ```

6. Install assets:

    ```bash
    bin/console assets:install
    ```

7. Clear cache:

    ```bash
    bin/console cache:clear
    ```
    
## Testing

```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -e test
$ bin/behat
$ bin/phpspec run
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
