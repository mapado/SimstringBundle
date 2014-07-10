<?php
namespace Mapado\SimstringBundle\DependencyInjection;

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
        $treeBuilder->root('mapado_simstring')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('databases')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                                ->then(
                                    function ($v) {
                                        return ['path' => $v];
                                    }
                                )
                        ->end()
                        ->children()
                            ->scalarNode('path')->end()
                            ->arrayNode('persistence')
                                ->children()
                                    ->enumNode('driver')
                                        ->isRequired()
                                        ->values(['orm', 'mongodb'])
                                    ->end()
                                    ->scalarNode('model')
                                        ->isRequired()
                                    ->end()
                                    ->scalarNode('field')
                                        ->isRequired()
                                    ->end()
                                    ->arrayNode('options')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('reader')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('database')->end()
                            ->enumNode('measure')
                                ->values(['cosine', 'dice', 'jaccard', 'overlap', 'exact'])
                                ->defaultValue('exact')
                            ->end()
                            ->floatNode('threshold')
                                ->min(0)->max(1)
                                ->defaultValue(1)
                            ->end()
                            ->integerNode('min_results')
                                ->min(1)
                                ->defaultValue(1)
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('writer')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('database')->end()
                            ->integerNode('ngram')
                                ->min(1)
                            ->end()
                            ->booleanNode('be')->end()
                            ->booleanNode('unicode')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
