<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DependencyInjection;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepository;
use Setono\SyliusMailchimpPlugin\Form\Type\AudienceType;
use Setono\SyliusMailchimpPlugin\Form\Type\MailchimpExportType;
use Setono\SyliusMailchimpPlugin\Model\Audience;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
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
                ->scalarNode('api_key')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Your Mailchimp API key')
                ->end()
                ->booleanNode('subscribe')->defaultTrue()->end()
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
                        ->arrayNode('audience')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Audience::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(AudienceInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(AudienceRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(AudienceType::class)->end()
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
