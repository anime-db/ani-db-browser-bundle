<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
     *     api:
     *         host: 'http://api.anidb.net:9001'
     *         prefix: '/httpapi/'
     *         protover: 1
     *     app:
     *         version: 2
     *         client: 'animedbplugin'
     *         code: 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
     *
     * @return ArrayNodeDefinition
     */
    public function getConfigTreeBuilder()
    {
        return (new TreeBuilder())
            ->root('anime_db_ani_db_browser')
                ->children()
                    ->arrayNode('api')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('host')
                                ->defaultValue('http://api.anidb.net:9001')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('prefix')
                                ->defaultValue('/httpapi/')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('protover')
                                ->defaultValue('1')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('app')
                        ->children()
                            ->scalarNode('version')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('client')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('code')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
