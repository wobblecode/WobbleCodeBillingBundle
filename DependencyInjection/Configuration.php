<?php

namespace WobbleCode\BillingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wobblecode_billing');

        $rootNode
            ->children()
                ->arrayNode('invoice')
                    ->children()
                        ->scalarNode('filesystem')->end()
                        ->scalarNode('pdf_path')->end()
                    ->end()
                ->end() // invoice
            ->end()
        ;

        return $treeBuilder;
    }
}
