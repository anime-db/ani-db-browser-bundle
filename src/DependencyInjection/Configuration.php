<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Config tree builder.
     *
     * Example config:
     *
     * anime_db_ani_db_browser:
     *     client: 'cache'
     *     app:
     *         version: 2
     *         client: 'animedbplugin'
     *         code: 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $tree_builder = new TreeBuilder();
        $tree_builder
            ->root('anime_db_ani_db_browser')
                ->children()
                    ->scalarNode('client')
                        ->cannotBeEmpty()
                        ->defaultValue('cache')
                    ->end()
                    ->arrayNode('app')
                        ->children()
                            ->integerNode('version')
                            ->end()
                            ->scalarNode('client')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('code')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $tree_builder;
    }
}
