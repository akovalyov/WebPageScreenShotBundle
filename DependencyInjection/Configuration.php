<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('idci_web_page_screen_shot');

        $rootNode
            ->children()
                ->scalarNode('phantomjs_bin_path')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('render')
                    ->scalarNode('width')
                        ->defaultValue('160')
                    ->end()
                    ->scalarNode('height')
                        ->defaultValue('144')
                    ->end()
                    ->scalarNode('mode')
                        ->defaultValue('default')
                    ->end()
                    ->scalarNode('format')
                        ->defaultValue('png')
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->booleanNode('enabled')
                        ->defaultValue('false')
                    ->end()
                    ->scalarNode('delay')
                        ->defaultValue(604800)
                    ->end()
                ->end()
            ->end()
        ;
                
                
        return $treeBuilder;
    }
}
