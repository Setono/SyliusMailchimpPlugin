<?php

/** @noinspection PhpUnusedLocalVariableInspection */

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DependencyInjection;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpConfigRepository;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepository;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpListRepository;
use Setono\SyliusMailchimpPlugin\Form\Type\MailchimpConfigType;
use Setono\SyliusMailchimpPlugin\Form\Type\MailchimpExportType;
use Setono\SyliusMailchimpPlugin\Form\Type\MailchimpListType;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfig;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExport;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpList;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('setono_sylius_mailchimp');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('setono_sylius_mailchimp');
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->booleanNode('subscribe')->defaultTrue()->end()
                ->booleanNode('queue')->defaultFalse()->end()
                ->arrayNode('merge_fields')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('first_name')->defaultValue('FNAME')->end()
                        ->scalarNode('last_name')->defaultValue('LNAME')->end()
                        ->scalarNode('address')->defaultValue('ADDRESS')->end()
                        ->scalarNode('phone')->defaultValue('PHONE')->end()
                        ->scalarNode('channel')->defaultValue(false)->end()
                        ->scalarNode('locale')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('config')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(MailchimpConfig::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(MailchimpConfigInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(MailchimpConfigRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(MailchimpConfigType::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('export')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(MailchimpExport::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(MailchimpExportInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(MailchimpExportRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(MailchimpExportType::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('list')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(MailchimpList::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(MailchimpListInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(MailchimpListRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(MailchimpListType::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
