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

    public function testSetClient()
    {
        $client = 'my_client';

        $this->assertEquals($this->configurator, $this->configurator->setClient($client));
        $this->assertEquals(['query' => ['client' => $client]], $this->configurator->getOptions());
    }

    public function testSetClientVersion()
    {
        $client_version = 'my_client_version';

        $this->assertEquals($this->configurator, $this->configurator->setClientVersion($client_version));
        $this->assertEquals(['query' => ['clientver' => $client_version]], $this->configurator->getOptions());
    }

    public function testSetProtocolVersion()
    {
        $protocol_version = 'my_protocol_version';

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
        $client = 'my_client';
        $client_version = 'my_clientver';
        $protocol_version = 'my_protover';
        $query = [
            'client' => 'bad_client',
            'clientver' => 'bad_clientver',
            'protover' => 'bad_protover',
            'request' => 'bad',
        ];
        $options = [
            'query' => [
                'client' => $client,
                'clientver' => $client_version,
                'protover' => $protocol_version,
                'request' => $request,
            ],
        ];

        $new = $this->configurator
            ->setClient($client)
            ->setClientVersion($client_version)
            ->setProtocolVersion($protocol_version)
            ->withRequest($request, $query);

        $this->assertEquals($options, $new->getOptions());
    }

    public function testGetOptions()
    {
        $this->assertEquals([], $this->configurator->getOptions());
    }
}
