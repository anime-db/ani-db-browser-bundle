<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Browser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Test browser
 *
 * @package AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class BrowserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Host
     *
     * @var string
     */
    protected $host = 'host';

    /**
     * API path prefix
     *
     * @var string
     */
    protected $api_prefix = 'api_prefix';

    /**
     * API client
     *
     * @var string
     */
    protected $api_client = 'api_client';

    /**
     * API clientver
     *
     * @var string
     */
    protected $api_clientver = 'api_clientver';

    /**
     * API protover
     *
     * @var string
     */
    protected $api_protover = 'api_protover';

    /**
     * App code
     *
     * @var string
     */
    protected $app_code = 'app_code';

    /**
     * Image prefix
     *
     * @var string
     */
    protected $image_prefix = 'image_prefix';

    /**
     * XML
     *
     * @var string
     */
    protected $xml = '<?xml version="1.0"?><root><text>Hello, world!</text></root>';

    /**
     * Browser
     *
     * @var \AnimeDb\Bundle\AniDbBrowserBundle\Service\Browser
     */
    protected $browser;

    /**
     * Client
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * Cache
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * Request
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * Response
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = $this
            ->getMockBuilder('\AnimeDb\Bundle\AniDbBrowserBundle\Service\CacheResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMock('\Guzzle\Http\Message\RequestInterface');
        $this->response = $this
            ->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->browser = new Browser(
            $this->client,
            $this->host,
            $this->api_prefix,
            $this->api_client,
            $this->api_clientver,
            $this->api_protover,
            $this->app_code,
            $this->image_prefix
        );
    }

    /**
     * Test get image url
     */
    public function testGetImageUrl()
    {
        $this->assertEquals($this->image_prefix.'foo', $this->browser->getImageUrl('foo'));
    }

    /**
     * Test get host
     */
    public function testGetHost()
    {
        $this->assertEquals($this->host, $this->browser->getHost());
    }

    /**
     * Test get api host
     */
    public function testGetApiHost()
    {
        $this->client
            ->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->browser->getApiHost());
    }

    /**
     * Test set timeout
     */
    public function testSetTimeout()
    {
        $timeout = 123;
        $this->client
            ->expects($this->once())
            ->method('setDefaultOption')
            ->with('timeout', $timeout);
        $this->assertEquals(
            $this->browser,
            $this->browser->setTimeout($timeout)
        );
    }

    /**
     * Test set proxy
     */
    public function testSetProxy()
    {
        $proxy = '127.0.0.1';
        $this->client
            ->expects($this->once())
            ->method('setDefaultOption')
            ->with('proxy', $proxy);
        $this->assertEquals(
            $this->browser,
            $this->browser->setProxy($proxy)
        );
    }

    /**
     * Test get failed transport
     *
     * @expectedException RuntimeException
     */
    public function testGetFailedTransport()
    {
        $this->buildDialogue('foo', ['bar' => 'baz']);
        $this->browser->get('foo', ['bar' => 'baz']);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $this->buildDialogue('foo', ['bar' => 'baz'], $this->xml);
        $result = $this->browser->get('foo', ['bar' => 'baz']);
        $this->assertInstanceOf('\Symfony\Component\DomCrawler\Crawler', $result);
        // objects are not identical, but their content should match
        $expected = new Crawler($this->xml);
        $this->assertEquals($expected->html(), $result->html());
    }

    /**
     * Build client dialogue
     *
     * @param string $request
     * @param array $params
     * @param string $data
     */
    protected function buildDialogue($request, array $params, $data = '')
    {
        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($this->getUrl($request, $params))
            ->will($this->returnValue($this->request));
        $this->request
            ->expects($this->once())
            ->method('setHeader')
            ->with('User-Agent', $this->app_code)
            ->will($this->returnValue($this->request));
        $this->request
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));
        $this->response
            ->expects($this->once())
            ->method('isError')
            ->will($this->returnValue(!$data));
        if ($data) {
            $this->response
                ->expects($this->once())
                ->method('getBody')
                ->with(true)
                ->will($this->returnValue(gzencode($data)));
        }
    }

    /**
     * Get URL
     *
     * @param string $request
     * @param array $params
     *
     * @return string
     */
    protected function getUrl($request, array $params)
    {
        return $this->api_prefix.
            (strpos($this->api_prefix, '?') !== false ? '&' : '?').
            http_build_query(array_merge([
                'client'    => $this->api_client,
                'clientver' => $this->api_clientver,
                'protover'  => $this->api_protover,
                'request'   => $request
            ], $params));
    }

    /**
     * Test get force
     */
    public function testGetForce()
    {
        $this->cache
            ->expects($this->never())
            ->method('get');
        $this->cache
            ->expects($this->once())
            ->method('set')
            ->with('foo', $this->getUrl('foo', ['bar' => 'baz']), $this->xml);

        $this->browser->setResponseCache($this->cache);
        $this->buildDialogue('foo', ['bar' => 'baz'], $this->xml);
        $this->browser->get('foo', ['bar' => 'baz'], true);
    }

    /**
     * Test get from cache
     */
    public function testGetFromCache()
    {
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($this->getUrl('foo', ['bar' => 'baz']))
            ->will($this->returnValue($this->xml));
        $this->cache
            ->expects($this->never())
            ->method('set');

        $this->browser->setResponseCache($this->cache);
        $this->browser->get('foo', ['bar' => 'baz']);
    }
}
