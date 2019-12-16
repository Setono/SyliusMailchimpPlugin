# Sylius Mailchimp Plugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

## Overview

This plugin has three main purposes:
1. Push your customers (as members/subscribers) to Mailchimp
2. Push your orders to Mailchimp utilizing their [ecommerce features](https://mailchimp.com/developer/guides/getting-started-with-ecommerce/)
3. Allow your customers to sign up for newsletters both in the checkout, but also using a form on your page

It does all this in a memory saving and performance optimized way.

## Installation

### 1. Install dependencies
This plugin uses the [Doctrine ORM Batcher bundle](https://github.com/Setono/DoctrineORMBatcherBundle). Install that first by following the instructions on that page.

### 2. Require plugin with composer:

```bash
$ composer require setono/sylius-mailchimp-plugin
```

### 3. Import configuration:

```yaml
# config/packages/setono_sylius_mailchimp.yaml
imports:
    - { resource: "@SetonoSyliusMailchimpPlugin/Resources/config/app/config.yaml" }
        
setono_sylius_mailchimp:
    api_key: '%env(resolve:MAILCHIMP_API_KEY)%'
```

Remember to update your `.env` and `.env.local` files:

```
# .env

###> setono/sylius-mailchimp-plugin ###
MAILCHIMP_API_KEY=
###< setono/sylius-mailchimp-plugin ###
```

```
# .env.local

###> setono/sylius-mailchimp-plugin ###
MAILCHIMP_API_KEY=INSERT YOUR API KEY HERE
###< setono/sylius-mailchimp-plugin ###
```

### 4. Import routing:
   
```yaml
# config/routes/setono_sylius_mailchimp.yaml
setono_sylius_mailchimp:
    resource: "@SetonoSyliusMailchimpPlugin/Resources/config/routing.yaml"
```

### 5. Add plugin class to your `bundles.php`:

```php
$bundles = [
    // ...
    
    // Notice that the Mailchimp plugin has be added before the SyliusGridBundle
    Setono\SyliusMailchimpPlugin\SetonoSyliusMailchimpPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    Setono\DoctrineORMBatcherBundle\SetonoDoctrineORMBatcherBundle::class => ['all' => true],
    
    // ...
];
```

Make sure you add the plugin **before** `SyliusGridBundle`. Otherwise you'll get exception like
`You have requested a non-existent parameter "setono_sylius_mailchimp.model.audience.class"`.

### 6. Override core classes

**Override `Customer` resource**
    
```php
<?php
// src/Entity/Customer.php

declare(strict_types=1);

namespace App\Entity;

use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface as SetonoSyliusMailchimpPluginCustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerTrait as SetonoSyliusMailchimpPluginCustomerTrait;
use Doctrine\ORM\Mapping as ORM;
    
/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
class Customer extends BaseCustomer implements SetonoSyliusMailchimpPluginCustomerInterface
{
    use SetonoSyliusMailchimpPluginCustomerTrait;
}
```

**Override `Order` resource**
```php
<?php
// src/Entity/Order.php

declare(strict_types=1);

namespace App\Entity;

use Sylius\Component\Core\Model\Order as BaseOrder;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface as SetonoSyliusMailchimpPluginOrderInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderTrait as SetonoSyliusMailchimpPluginOrderTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
class Order extends BaseOrder implements SetonoSyliusMailchimpPluginOrderInterface
{
    use SetonoSyliusMailchimpPluginOrderTrait;
}

```

**Create `CustomerRepository.php`**

```php
<?php
// src/Doctrine/ORM/CustomerRepository.php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface as SetonoSyliusMailchimpPluginCustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryTrait as SetonoSyliusMailchimpPluginCustomerRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;

class CustomerRepository extends BaseCustomerRepository implements SetonoSyliusMailchimpPluginCustomerRepositoryInterface
{
    use SetonoSyliusMailchimpPluginCustomerRepositoryTrait;
}
```

**Create `OrderRepository.php`**

```php
<?php
// src/Doctrine/ORM/OrderRepository.php

declare(strict_types=1);

namespace Tests\Setono\SyliusMailchimpPlugin\Application\Doctrine\ORM;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\OrderRepositoryInterface as SetonoSyliusMailchimpPluginOrderRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\OrderRepositoryTrait as SetonoSyliusMailchimpPluginOrderRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;

class OrderRepository extends BaseOrderRepository implements SetonoSyliusMailchimpPluginOrderRepositoryInterface
{
    use SetonoSyliusMailchimpPluginOrderRepositoryTrait;
}

```

**Add configuration** 

```yaml
# config/packages/_sylius.yaml
sylius_customer:
    resources:
        customer:
            classes:
                model: App\Entity\Customer
                repository: App\Doctrine\ORM\CustomerRepository
                
sylius_order:
    resources:
        order:
            classes:
                model: App\Entity\Order
                repository: App\Doctrine\ORM\OrderRepository
```

### 7. Update your database:

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

### 8. Install assets:

```bash
$ php bin/console assets:install
```

### Step 9: Using asynchronous transport (optional, but very recommended)

All commands in this plugin will extend the [CommandInterface](src/Message/Command/CommandInterface.php).
Therefore you can route all commands easily by adding this to your [Messenger config](https://symfony.com/doc/current/messenger.html#routing-messages-to-a-transport):

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            # Route all command messages to the async transport
            # This presumes that you have already set up an 'async' transport
            # See docs on how to setup a transport like that: https://symfony.com/doc/current/messenger.html#transports-async-queued-messages
            'Setono\SyliusMailchimpPlugin\Message\Command\CommandInterface': async
```

### 10. Clear cache:

```bash
$ php bin/console cache:clear
```

### 11. Define fixtures

```yaml
# fixtures.yaml

sylius_fixtures:
	suites:
		default:
			fixtures:
				setono_mailchimp:
					options:
                        custom:
                          - name: 'United States audience'
                            audience_id: '0598aea4e3'
                            channel: 'FASHION_WEB'
                          - name: 'Denmark audience'
                            audience_id: '0e23b9524f'
                            channel: 'DK_WEB'
```

## Usage

### Push customers to Mailchimp lists
By default your customers will be pushed to Mailchimp lists when they are updated or when they haven't yet been pushed.

Run the following command to push customers:

```bash
$ php bin/console setono:sylius-mailchimp:push-customers
```

### Push orders to Mailchimp ecommerce
For orders it's the same thing; if they haven't been pushed or they are updated they will be pushed.

Run the following command to push orders:

```bash
$ php bin/console setono:sylius-mailchimp:push-orders
```

### Insert subscription form
To insert the subscription form anywhere on your site, just do the following in twig:

```twig
{% include '@SetonoSyliusMailchimpPlugin/Shop/subscribe.html.twig' %}
```

You can of course also use the [BlockEventListener](https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/UiBundle/Block/BlockEventListener.php):

```xml
<service id="app.block_event_listener.shop.subscribe_to_newsletter" class="Sylius\Bundle\UiBundle\Block\BlockEventListener">
    <argument>@SetonoSyliusMailchimpPlugin/Shop/subscribe.html.twig</argument>

    <tag name="kernel.event_listener" event="sonata.block.event.sylius.shop.layout.after_footer" method="onBlockEvent"/>
</service>
```

In this case - you should disable default block event listener: 

```yaml
setono_sylius_mailchimp:
  subscribe: false
```

## Contribution

Run `composer try` to setup plugin environment and try test application.

Please, run `composer all` before pusing changes to run all checks and tests.

### Testing

Run `composer tests` to run all tests.

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-mailchimp-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusMailchimpPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusMailchimpPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-mailchimp-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusMailchimpPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusMailchimpPlugin
