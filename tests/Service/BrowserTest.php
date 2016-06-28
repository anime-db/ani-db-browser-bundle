<?php
/**
 * AnimeDb package
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Browser;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\CacheResponse;
use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Symfony\Component\DomCrawler\Crawler;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $host = 'host';

    /**
     * @var string
     */
    protected $api_prefix = 'api_prefix';

    /**
     * @var string
     */
    protected $api_client = 'api_client';

    /**
     * @var string
     */
    protected $api_clientver = 'api_clientver';

    /**
     * @var string
     */
    protected $api_protover = 'api_protover';

    /**
     * @var string
     */
    protected $app_code = 'app_code';

    /**
     * @var string
     */
    protected $image_prefix = 'image_prefix';

    /**
     * @var string
     */
    protected $xml = '<?xml version="1.0"?><root><text>Hello, world!</text></root>';

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CacheResponse
     */
    protected $cache;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestInterface
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Response
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResponseRepair
     */
    protected $response_repair;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = $this
            ->getMockBuilder('AnimeDb\Bundle\AniDbBrowserBundle\Service\CacheResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $this->response_repair = $this->getMock('AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair');
        $this->response = $this
            ->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->browser = new Browser(
            $this->client,
            $this->response_repair,
            $this->host,
            $this->api_prefix,
            $this->api_client,
            $this->api_clientver,
            $this->api_protover,
            $this->app_code,
            $this->image_prefix
        );
    }

    public function testGetImageUrl()
    {
        $this->assertEquals($this->image_prefix.'foo', $this->browser->getImageUrl('foo'));
    }

    public function testGetHost()
    {
        $this->assertEquals($this->host, $this->browser->getHost());
    }

    public function testGetApiHost()
    {
        $this->client
            ->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->browser->getApiHost());
    }

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
     * @expectedException \RuntimeException
     */
    public function testGetFailedTransport()
    {
        $this->buildDialogue('foo', ['bar' => 'baz']);
        $this->browser->getCrawler('foo', ['bar' => 'baz']);
    }

    public function testGet()
    {
        $this->buildDialogue('foo', ['bar' => 'baz'], $this->xml);
        $result = $this->browser->getCrawler('foo', ['bar' => 'baz']);

        $this->assertInstanceOf('\Symfony\Component\DomCrawler\Crawler', $result);

        // objects are not identical, but their content should match
        $expected = new Crawler($this->xml);
        $this->assertEquals($expected->html(), $result->html());
    }

    /**
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
            $this->response_repair
                ->expects($this->once())
                ->method('repair')
                ->with($data)
                ->will($this->returnValue($data));
        } else {
            $this->response
                ->expects($this->never())
                ->method('getBody');
            $this->response_repair
                ->expects($this->never())
                ->method('repair');
        }
    }

    /**
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
        $this->browser->getCrawler('foo', ['bar' => 'baz'], true);
    }

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
        $this->browser->getCrawler('foo', ['bar' => 'baz']);
    }
}
