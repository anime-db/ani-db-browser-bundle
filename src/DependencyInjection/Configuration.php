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
     *     client: 'cache'
     *     image_prefix: 'http://img7.anidb.net/pics/anime/'
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
                    ->scalarNode('client')
                        ->cannotBeEmpty()
                        ->defaultValue('cache')
                    ->end()
                    ->scalarNode('image_prefix')
                        ->defaultValue('http://img7.anidb.net/pics/anime/')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->append($this->api())
                ->append($this->app())
            ->end()
        ;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function api()
    {
        return (new TreeBuilder())
            ->root('app')
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
                    ->integerNode('protover')
                        ->defaultValue('1')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->isRequired()
            ;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function app()
    {
        return (new TreeBuilder())
            ->root('app')
                ->children()
                    ->integerNode('version')
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
        ;
    }
}
