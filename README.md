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

### 6. Include the newsletter in your template:

```twig
{% include '@SetonoSyliusMailChimpPlugin/Shop/_subscribe.html.twig' %}
```

Add these Javascripts to the layout template that includes your subscription form imported in the previous steps
```html
<script src="{{ asset(path) }}"></script>
<script src="{{ asset('bundles/setonosyliusmailchimpplugin/setono-mailchimp-subscribe.js') }}"></script>
<script>
    $('#footer-newsletter-form').joinNewsletter();
</script>
```

That's the simplest and fastest way to integrate the jQuery plugin. If you need to customize it, simply take a look at
[setono-mailchimp-subscribe.js](src/Resources/public/setono-mailchimp-subscribe.js), create your own `*.js` plugin and 
import it in your main `gulpfile.babel.js`.

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
