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

#### (optional) Add transport for enqueue bundle

(see https://github.com/php-enqueue/enqueue-dev/blob/master/docs/bundle/quick_tour.md
for more details)

```bash
composer require enqueue/fs
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
    
    // Uncomment if you want to use queues
    // Enqueue\Bundle\EnqueueBundle::class => ['all' => true],
];
```

Make sure you've added it **before** `SyliusGridBundle`. Otherwise you'll get exception like
`You have requested a non-existent parameter "setono_sylius_mailchimp.model.export.class"`.

### 5. Override core classes

- Override `Customer` entity:
     
    - If you use `annotations` mapping:
    
        ```php
        <?php
        
        # src/Entity/Customer.php 
  
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
    
    - If you use `xml` mapping:
    
        ```php
        <?php
        
        # src/Model/Customer.php 
  
        declare(strict_types=1);
        
        namespace App\Entity;
        
        use Sylius\Component\Core\Model\Customer as BaseCustomer;
        use Setono\SyliusMailchimpPlugin\Model\CustomerInterface as SetonoSyliusMailchimpPluginCustomerInterface;
        use Setono\SyliusMailchimpPlugin\Model\CustomerTrait as SetonoSyliusMailchimpPluginCustomerTrait;
        
        class Customer extends BaseCustomer implements SetonoSyliusMailchimpPluginCustomerInterface
        {
            use SetonoSyliusMailchimpPluginCustomerTrait {
                SetonoSyliusMailchimpPluginCustomerTrait::__construct as private __setonoSyliusMailchimpPluginCustomerTraitConstruct;
            }
        
            public function __construct()
            {
                parent::__construct();
                $this->__setonoSyliusMailchimpPluginCustomerTraitConstruct();
            }
        }
        ```
        
        ```xml
        <?xml version="1.0" encoding="UTF-8"?>
        
        <!-- config/doctrine/model/Product.orm.xml -->
        
        <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                          xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                              http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
        
            <mapped-superclass name="App\Model\Customer" table="sylius_customer">
                ...
            </mapped-superclass>
        
        </doctrine-mapping>
        ```

- Create `Doctrine/ORM/CustomerRepository.php`:

    ```php
    <?php
    
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

- Add configuration: 

    ```yaml
    # config/packages/_sylius.yaml
    sylius_customer:
        resources:
            customer:
                classes:
                    model: App\Entity\Customer # or App\Model\Customer
                    repository: App\Doctrine\ORM\CustomerRepository
    ```

- Check:

    ```bash
    bin/console doctrine:mapping:info | grep Customer
    
    # You should see:
    # [OK]   App\Entity\Customer 
    # or: 
    # [OK]   App\Model\Customer
    
    bin/console debug:container | grep CustomerRepository
    
    # You should see:
    # sylius.repository.customer    App\Doctrine\ORM\CustomerRepository
    ```

### 5. Update your database:

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

### 6. Make plugin configurations:

#### 1. (optional) Enable and add proper enqueue bundle configuration

```yaml
# config/packages/setono_sylius_mailchimp.yaml

setono_sylius_mailchimp:
    queue: true

enqueue:
    transport:
        # Here we use enqueue/fs for testing as most simple transport implementation
        # @see https://enqueue.readthedocs.io/en/latest/transport/filesystem/
        default: fs
        fs:
            dsn: "file://%kernel.project_dir%/var/queue"
    client:
        traceable_producer: true
```

#### 2. Subscriptions form at shop's footer

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

#### 3. (optional) Create and/or configure Mailchimp Merge Fields

  - If you don't want to collect channel and locale of subscribers,
    (your shop works with just one channel and locale, for example)
    you can skip this step, as far as by default, plugin configured 
    to work with existing (Mailchimp's default) Merge Fields only:

    ```yaml
    # config/packages/setono_sylius_mailchimp.yaml
    setono_sylius_mailchimp:
        merge_fields:
            first_name: FNAME # Mailchimp's default
            last_name: LNAME  # Mailchimp's default
            address: ADDRESS  # Mailchimp's default
            phone: PHONE      # Mailchimp's default
            channel: false    # Which means don't store subscriber's channel at mailchimp side
            locale: false     # Which means don't store subscriber's locale at mailchimp side
    ```
    
  - If you want to use channel and locale Merge Fields:
  
    - Add desired Merge Fields at Mailchimp side like this:
      
      ![Screenshot showing how to configure mailchimp mergefields](docs/images/mailchimp-list-mergefields.png)
      
      You can configure both channel and locale mergefield's type as `text` or as `dropdown`.
    
    - Configure plugin to use that Merge Fields names:
  
        ```yaml
        # config/packages/setono_sylius_mailchimp.yaml
        setono_sylius_mailchimp:
            merge_fields:
                channel: CHANNEL
                locale: LOCALE
        ```
        
      (You can use any other names for sure - `CHANNEL_CODE`, `LOCALE_CODE`, for example)

### 7. Install assets:

```bash
$ php bin/console assets:install --symlink
```

### 8. Clear cache:

```bash
$ php bin/console cache:clear
```

### 9. (production) Configure CRON jobs 

- To create exports - every day:

    ```bash
    * 03 * * * path/to/php path/to/bin/console setono:mailchimp:export:create -n
    ````

- To handle exports - every minute:

    ```bash
    01 * * * * path/to/php path/to/bin/console setono:mailchimp:export:handle --limit=30
    ````
    
    Providing such `limit` allow to export 30 customers per minute. 

### 10. (production) Configure `supervisord` if you use queues 

Make sure next command always in run state:

```bash
path/to/bin/console enqueue:consume -vvv
```

This can be done with `supervisord`
(see [docs](https://enqueue.readthedocs.io/en/latest/bundle/production_settings/) for details):

```
[program:enqueue_message_consumer]
command=/path/to/bin/console --env=prod --no-debug --time-limit="now + 5 minutes" enqueue:consume --message-limit=1
process_name=%(program_name)s_%(process_num)02d
numprocs=4
autostart=true
autorestart=true
startsecs=0
user=apache
redirect_stderr=true
```

## Usage

- You can now configure Mailchimp lists in your admin UI and later on 
  export them from via admin or the following command:

    ```bash
    $ php bin/console setono:mailchimp:export:create
    ````

- To handle created export, run: 

    ```bash
    $ php bin/console setono:mailchimp:export:handle
    ````

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
