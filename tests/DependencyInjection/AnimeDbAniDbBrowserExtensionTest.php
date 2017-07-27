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
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private $container;

    /**
     * @var AnimeDbAniDbBrowserExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->extension = new AnimeDbAniDbBrowserExtension();
    }

    /**
     * @return array
     */
    public function config()
    {
        return [
            [
                [
                    'anime_db_ani_db_browser' => [
                        'app' => [
                            'version' => 1,
                            'client' => 'custom',
                            'code' => 'api-team-XXXXXXXXXXX',
                        ],
                    ],
                ],
                'http://api.anidb.net:9001',
                '/httpapi/',
                1,
                1,
                'custom',
                'api-team-XXXXXXXXXXX',
            ],
            [
                [
                    'anime_db_ani_db_browser' => [
                        'api' => [
                            'host' => 'https://api.anidb.net',
                            'prefix' => '/api/',
                            'protover' => 2,
                        ],
                        'app' => [
                            'version' => 2,
                            'client' => 'animedbplugin',
                            'code' => 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                        ],
                    ],
                ],
                'https://api.anidb.net',
                '/api/',
                2,
                2,
                'animedbplugin',
                'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ],
        ];
    }

    /**
     * @dataProvider config
     *
     * @param array  $config
     * @param string $api_host
     * @param string $api_prefix
     * @param int    $api_protover
     * @param int    $app_version
     * @param string $app_client
     * @param string $app_code
     */
    public function testLoad(
        array $config,
        $api_host,
        $api_prefix,
        $api_protover,
        $app_version,
        $app_client,
        $app_code
    ) {
        $browser = $this->getMock(Definition::class);
        $browser
            ->expects($this->at(0))
            ->method('replaceArgument')
            ->with(3, $api_host)
            ->will($this->returnSelf())
        ;
        $browser
            ->expects($this->at(1))
            ->method('replaceArgument')
            ->with(4, $api_prefix)
            ->will($this->returnSelf())
        ;
        $browser
            ->expects($this->at(2))
            ->method('replaceArgument')
            ->with(5, $api_protover)
            ->will($this->returnSelf())
        ;
        $browser
            ->expects($this->at(3))
            ->method('replaceArgument')
            ->with(6, $app_version)
            ->will($this->returnSelf())
        ;
        $browser
            ->expects($this->at(4))
            ->method('replaceArgument')
            ->with(7, $app_client)
            ->will($this->returnSelf())
        ;
        $browser
            ->expects($this->at(5))
            ->method('replaceArgument')
            ->with(8, $app_code)
            ->will($this->returnSelf())
        ;

        $this->container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('anime_db.ani_db.browser')
            ->will($this->returnValue($browser))
        ;

        $this->extension->load($config, $this->container);
    }
}
