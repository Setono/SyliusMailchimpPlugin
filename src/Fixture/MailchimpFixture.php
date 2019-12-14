<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class MailchimpFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'setono_mailchimp';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('audience_id')->cannotBeEmpty()->end()
                ->scalarNode('channel')->cannotBeEmpty()->end()
        ;
    }
}
