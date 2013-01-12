<?php

namespace Dizda\CloudBackupBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('dizda_cloud_backup');

        $rootNode
        ->children()
            ->arrayNode('cloud_storages')
                ->children()
                    ->arrayNode('dropbox')
                    ->info('Dropbox account credentials (use parameters in config.yml and store real values in prameters.yml)')
                        ->children()
                            ->scalarNode('user')->isRequired()->end()
                            ->scalarNode('password')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('cloudapp')
                    ->info('CloudApp')
                        ->children()
                            ->scalarNode('user')->isRequired()->end()
                            ->scalarNode('password')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('databases')
                ->children()
                    ->arrayNode('mongodb')
                        ->children()
                            ->booleanNode('all_databases')->defaultTrue()->end()
                            ->scalarNode('database')->defaultFalse()->end()
                            ->scalarNode('db_user')->defaultValue(null)->end()
                            ->scalarNode('db_password')->defaultValue(null)->end()
                        ->end()
                    ->end()
                    /*->booleanNode('mongodb')->defaultFalse()->end()*/
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
