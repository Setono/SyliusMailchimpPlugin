# Sylius Mailchimp Plugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

## Installation

### 1. Require plugin with composer:

```bash
composer require setono/sylius-mailchimp-plugin
```

### 2. Import configuration:

```yaml
imports:
    - { resource: "@SetonoSyliusMailchimpPlugin/Resources/config/config.yml" }
```

### 3. Import routing:
   
```yaml
setono_sylius_mailchimp:
    resource: "@SetonoSyliusMailchimpPlugin/Resources/config/routing.yml"
```

### 4. Add plugin class to your `AppKernel`:

```php
$bundles = [
    new \Setono\SyliusMailchimpPlugin\SetonoSyliusMailchimpPlugin(),
];
```

### 5. Update your database:

```bash
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
```

### 6. Install assets:

```bash
bin/console assets:install
```

### 7. Clear cache:

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

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-mailchimp-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusMailchimpPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusMailchimpPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-mailchimp-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusMailchimpPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusMailchimpPlugin
