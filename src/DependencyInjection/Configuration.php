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
        return (new TreeBuilder())
            ->root('anime_db_ani_db_browser')
                ->children()
                    ->scalarNode('client')
                        ->cannotBeEmpty()
                        ->defaultValue('cache')
                    ->end()
                ->end()
                ->append($this->getApp())
            ->end();
    }

    /**
     * @return TreeBuilder
     */
    protected function getApp()
    {
        return (new TreeBuilder())
            ->root('app')
                ->isRequired()
                ->children()
                    ->integerNode('version')
                        ->isRequired()
                    ->end()
                    ->scalarNode('client')
                        ->cannotBeEmpty()
                        ->isRequired()
                    ->end()
                    ->scalarNode('code')
                        ->cannotBeEmpty()
                        ->isRequired()
                    ->end()
                ->end();
    }
}
