<?php

/** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder;

final class MailchimpConfigFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'setono_sylius_mailchimp_config';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        /** @var NodeBuilder $node */
        $node = $resourceNode->children();
        $node->scalarNode('code')->cannotBeEmpty();
        $node->scalarNode('store_id')->cannotBeEmpty();
        $node->scalarNode('api_key')->cannotBeEmpty();
        $node->booleanNode('export_all')->defaultNull();

        /** @var NodeBuilder $listsNode */
        $listsNode = $node->arrayNode('lists')
            ->requiresAtLeastOneElement()
            ->arrayPrototype()
                ->children()
        ;
        $listsNode->scalarNode('list_id');
        $listsNode->arrayNode('channels')->scalarPrototype();
        $listsNode->arrayNode('locales')->scalarPrototype();
    }
}
