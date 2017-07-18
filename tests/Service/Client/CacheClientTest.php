<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service\Client;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\ExpireResolver;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\Storage\Storage;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\CacheClient;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Client;

class CacheClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ExpireResolver
     */
    private $resolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    private $storage;

    /**
     * @var CacheClient
     */
    private $cache_client;

    protected function setUp()
    {
        $this->client = $this->getMock(Client::class);
        $this->resolver = $this
            ->getMockBuilder(ExpireResolver::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->storage = $this->getMock(Storage::class);

        $this->cache_client = new CacheClient($this->client, $this->resolver, $this->storage);
    }

    public function testSetTimeout()
    {
        $timeout = 123;
        $this->client
            ->expects($this->once())
            ->method('setTimeout')
            ->with($timeout)
            ->will($this->returnSelf())
        ;

        $this->assertEquals($this->cache_client, $this->cache_client->setTimeout($timeout));
    }

    public function testSetProxy()
    {
        $proxy = '127.0.0.1';
        $this->client
            ->expects($this->once())
            ->method('setProxy')
            ->with($proxy)
            ->will($this->returnSelf())
        ;

        $this->assertEquals($this->cache_client, $this->cache_client->setProxy($proxy));
    }

    public function testGetNotNeedCache()
    {
        $request = 'foo';
        $response = 'bar';
        $params = ['foo' => 'bar'];

        $this->resolver
            ->expects($this->once())
            ->method('getExpire')
            ->with($request, $this->isInstanceOf(\DateTime::class))
            ->will($this->returnValue(null))
        ;

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($request, $params)
            ->will($this->returnValue($response))
        ;

        $this->storage
            ->expects($this->never())
            ->method('get')
        ;

        $this->assertEquals($response, $this->cache_client->get($request, $params));
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return [
            ['request=foo', 'foo', []],
            ['request=foo&bar=baz', 'foo', ['bar' => 'baz']],
            ['request=foo&bar=baz', 'foo', ['bar' => 'baz', 'request' => 'bar']],
        ];
    }

    /**
     * @dataProvider getRequestParams
     *
     * @param string $key
     * @param string $request
     * @param array $params
     */
    public function testGetFromCache($key, $request, array $params)
    {
        $response = 'bar';
        $expires = new \DateTime();

        $this->resolver
            ->expects($this->once())
            ->method('getExpire')
            ->with($request, $this->isInstanceOf(\DateTime::class))
            ->will($this->returnValue($expires))
        ;

        $this->client
            ->expects($this->never())
            ->method('get')
        ;

        $this->storage
            ->expects($this->once())
            ->method('get')
            ->with($key)
            ->will($this->returnValue($response))
        ;

        $this->assertEquals($response, $this->cache_client->get($request, $params));
    }

    /**
     * @dataProvider getRequestParams
     *
     * @param string $key
     * @param string $request
     * @param array $params
     */
    public function testGetNoCache($key, $request, $params)
    {
        $response = 'bar';
        $expires = new \DateTime();

        $this->resolver
            ->expects($this->once())
            ->method('getExpire')
            ->with($request, $this->isInstanceOf(\DateTime::class))
            ->will($this->returnValue($expires))
        ;

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($request, $params)
            ->will($this->returnValue($response))
        ;

        $this->storage
            ->expects($this->once())
            ->method('get')
            ->with($key)
            ->will($this->returnValue(null))
        ;

        $this->storage
            ->expects($this->once())
            ->method('set')
            ->with($key, $response, $expires)
        ;

        $this->assertEquals($response, $this->cache_client->get($request, $params));
    }
}
