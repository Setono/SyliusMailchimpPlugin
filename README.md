# Sylius Mailchimp Plugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

## Overview

The plugin allows configuring various MailChimp lists, exporting emails via admin panel & CLI and signing to the newsletter 
from the shop. It extends [BitBag/SyliusMailChimpPlugin](https://github.com/BitBagCommerce/SyliusMailChimpPlugin) and is
developed with the contribution of the BitBag team.  

## Installation

### 1. Require plugin with composer:

```bash
$ composer require setono/sylius-mailchimp-plugin
```

### 2. Import configuration:

```yaml
# config/packages/_sylius.yaml
imports:
    - { resource: "@SetonoSyliusMailchimpPlugin/Resources/config/app/config.yaml" }
```

### 3. Import routing:
   
```yaml
# config/routes/setono_sylius_mailchimp.yaml
setono_sylius_mailchimp:
    resource: "@SetonoSyliusMailchimpPlugin/Resources/config/routes.yaml"
```

### 4. Add plugin class to your `bundles.php`:

```php
$bundles = [
    Setono\SyliusMailchimpPlugin\SetonoSyliusMailchimpPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
];
```

Make sure you've added it **before** `SyliusGridBundle`. Otherwise you'll get exception like
`You have requested a non-existent parameter "setono_sylius_mailchimp.model.export.class"`.

### 5. Update your database:

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

### 6. Configure subscription form:

- By default, subscription form will be added to footer via block events.

- If you want to disable subscription form, configure plugin like this: 

    ```yaml
    # config/packages/setono_sylius_mailchimp.yaml
    setono_sylius_mailchimp:
        subscribe: false
    ```
 
- If you want to add subscription form to custom place:

  - Configure plugin like this to disable automatic form inclusion to footer:
   
    ```yaml
    # config/packages/setono_sylius_mailchimp.yaml
    setono_sylius_mailchimp:
        subscribe: false
    ```

  - Include the subscribe form in your template to place you need:

    ```twig
    {# templates/bundles/SyliusShopBundle/_footer.html.twig #}

    {% include '@SetonoSyliusMailchimpPlugin/Shop/Subscribe/_form.html.twig' %}
    ```
    
    See example at `tests/Application/templates/bundles/SyliusShopBundle/_footer.html.twig`.

### 7. Install assets:

```bash
$ php bin/console assets:install --symlink
```

### 8. Clear cache:

```bash
$ php bin/console cache:clear
```

## Usage

You can now configure Mailchimp lists in your admin UI and later on export them from via admin or the following command:

```bash
$ php bin/console setono:mailchimp:export
````

## Testing

Run `composer tests` to run all tests.

## Contribution

Run `composer all` before pusing changes to run all checks and tests.

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-mailchimp-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusMailchimpPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusMailchimpPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-mailchimp-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusMailchimpPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusMailchimpPlugin
