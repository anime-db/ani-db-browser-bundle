<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class AnimeDbAniDbBrowserExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container
            ->getDefinition('anime_db.ani_db.browser.client.guzzle.request_configurator')
            ->addMethodCall('setAppVersion', [$config['app']['version']])
            ->addMethodCall('setAppClient', [$config['app']['client']])
            ->addMethodCall('setAppCode', [$config['app']['code']])
        ;
        $container->getDefinition('anime_db.ani_db.browser.client.guzzle.guzzle')->replaceArgument(0, [
            'base_uri' => $config['api']['host']
        ]);
        $container->getDefinition('anime_db.ani_db.browser')
            ->replaceArgument(2, $config['api']['host'])
            ->replaceArgument(3, $config['image_prefix'])
        ;
        $container->getDefinition('anime_db.ani_db.browser.client.guzzle')
            ->replaceArgument(3, $config['api']['prefix'])
        ;
        $container->getDefinition('anime_db.ani_db.browser.client.guzzle.request_configurator')->setMethodCalls([
            'setProtocolVersion' => [$config['api']['protover']],
        ]);

        $container->setAlias('anime_db.ani_db.browser.client', $this->realClientServiceName($config['client']));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function realClientServiceName($name)
    {
        if (in_array($name, ['cache', 'guzzle'])) {
            return 'anime_db.ani_db.browser.client.'.$name;
        }

        return $name;
    }
}
