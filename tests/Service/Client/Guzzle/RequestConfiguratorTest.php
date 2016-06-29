<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service\Client\Guzzle;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Guzzle\RequestConfigurator;

class RequestConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestConfigurator
     */
    protected $configurator;

    protected function setUp()
    {
        $this->configurator = new RequestConfigurator();
    }

    public function testSetAppCode()
    {
        $app_code = 'my_app_code';

        $this->assertEquals($this->configurator, $this->configurator->setAppCode($app_code));
        $this->assertEquals(['headers' => ['User-Agent' => $app_code]], $this->configurator->getOptions());
    }

    public function testSetTimeout()
    {
        $timeout = 123;

        $this->assertEquals($this->configurator, $this->configurator->setTimeout($timeout));
        $this->assertEquals(['timeout' => $timeout], $this->configurator->getOptions());
    }

    public function testSetProxy()
    {
        $proxy = '127.0.0.1';

        $this->assertEquals($this->configurator, $this->configurator->setProxy($proxy));
        $this->assertEquals(['proxy' => $proxy], $this->configurator->getOptions());
    }

    public function testSetAppClient()
    {
        $app_client = 'my_app_client';

        $this->assertEquals($this->configurator, $this->configurator->setAppCode($app_client));
        $this->assertEquals(['query' => ['client' => $app_client]], $this->configurator->getOptions());
    }

    public function testSetVersion()
    {
        $app_version = 123;

        $this->assertEquals($this->configurator, $this->configurator->setAppVersion($app_version));
        $this->assertEquals(['query' => ['clientver' => $app_version]], $this->configurator->getOptions());
    }

    public function testSetProtocolVersion()
    {
        $protocol_version = 123;

        $this->assertEquals($this->configurator, $this->configurator->setProtocolVersion($protocol_version));
        $this->assertEquals(['query' => ['protover' => $protocol_version]], $this->configurator->getOptions());
    }

    public function testWithRequest()
    {
        $request = 'foo';

        $new = $this->configurator->withRequest($request, []);

        $this->assertInstanceOf(RequestConfigurator::class, $new);
        $this->assertEquals(['query' => ['request' => $request]], $new->getOptions());
    }

    public function testWithRequestClone()
    {
        $new = $this->configurator->withRequest('foo', []);

        $this->assertEquals($new, $new->setTimeout(123));
        $this->assertNotEquals($this->configurator, $new);
    }

    public function testWithRequestTryOverrideQueryParameters()
    {
        $request = 'foo';
        $app_client = 'my_app_client';
        $app_version = 123;
        $protocol_version = 456;
        $query = [
            'client' => 'bad_client',
            'clientver' => 0,
            'protover' => 0,
            'request' => 'bad',
        ];
        $options = [
            'query' => [
                'client' => $app_client,
                'clientver' => $app_version,
                'protover' => $protocol_version,
                'request' => $request,
            ],
        ];

        $new = $this->configurator
            ->setAppClient($app_client)
            ->setAppVersion($app_version)
            ->setProtocolVersion($protocol_version)
            ->withRequest($request, $query);

        $this->assertEquals($options, $new->getOptions());
    }

    public function testGetOptions()
    {
        $this->assertEquals([], $this->configurator->getOptions());
    }
}
