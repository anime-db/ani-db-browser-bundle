<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection\AnimeDbAniDbBrowserExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AnimeDbAniDbBrowserExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getConfigClients()
    {
        return [
            ['', 'anime_db.ani_db.browser.client.cache'],
            ['cache', 'anime_db.ani_db.browser.client.cache'],
            ['guzzle', 'anime_db.ani_db.browser.client.guzzle'],
            ['custom_client', 'custom_client'],
        ];
    }

    /**
     * @dataProvider getConfigClients
     *
     * @param string $client
     * @param string $service
     */
    public function testLoad($client, $service)
    {
        $config = [
            'anime_db_ani_db_browser' => [
                'app' => [
                    'version' => 2,
                    'client' => 'animedbplugin',
                    'code' => 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                ],
            ],
        ];

        if ($client) {
            $config['anime_db_ani_db_browser']['client'] = $client;
        }

        $i = -1;
        $configurator = $this->getMock(Definition::class);
        $configurator
            ->expects($this->at(++$i))
            ->method('addMethodCall')
            ->with('setAppVersion', [$config['anime_db_ani_db_browser']['app']['version']])
            ->will($this->returnSelf());
        $configurator
            ->expects($this->at(++$i))
            ->method('addMethodCall')
            ->with('setAppClient', [$config['anime_db_ani_db_browser']['app']['client']])
            ->will($this->returnSelf());
        $configurator
            ->expects($this->at(++$i))
            ->method('addMethodCall')
            ->with('setAppCode', [$config['anime_db_ani_db_browser']['app']['code']])
            ->will($this->returnSelf());

        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('anime_db.ani_db.browser.client.guzzle.request_configurator')
            ->will($this->returnValue($configurator));
        $container
            ->expects($this->once())
            ->method('setAlias')
            ->with(
                'anime_db.ani_db.browser.client',
                $service
            );

        $di = new AnimeDbAniDbBrowserExtension();
        $di->load($config, $container);
    }

    /**
     * @return array
     */
    public function getConfigApps()
    {
        return [
            [[]],
            [['version' => 1]],
            [['client' => 'animedbplugin']],
            [['code' => 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX']],
            [['version' => 1, 'client' => 'animedbplugin']],
            [['version' => 1, 'code' => 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX']],
            [['client' => 'animedbplugin', 'code' => 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX']],
        ];
    }

    /**
     * @dataProvider getConfigApps
     * @expectedException \RuntimeException
     *
     * @param array $app
     */
    public function testLoadFailed(array $app)
    {
        $config = ['anime_db_ani_db_browser' => []];

        if ($app) {
            $config['anime_db_ani_db_browser']['app'] = $app;
        }

        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this->getMock(ContainerBuilder::class);

        $di = new AnimeDbAniDbBrowserExtension();
        $di->load($config, $container);
    }
}
