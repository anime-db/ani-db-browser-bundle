<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Tests;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Browser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Test browser
 *
 * @package AnimeDb\Bundle\AniDbBrowserBundle\Service\Tests
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
     * Image prefix
     *
     * @var string
     */
    protected $image_prefix = 'image_prefix';

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
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->client = $this
            ->getMockBuilder('\Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->browser = new Browser(
            $this->client,
            $this->host,
            $this->api_prefix,
            $this->api_client,
            $this->api_clientver,
            $this->api_protover,
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
     * Test get failed transport
     *
     * @expectedException RuntimeException
     */
    public function testGetFailedTransport()
    {
        $this->buildDialogue('foo', array('bar' => 'baz'));
        $this->browser->get('foo', array('bar' => 'baz'));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $xml = '<?xml version="1.0"?><root><text>Hello, world!</text></root>';
        $expected = new Crawler($xml);

        $this->buildDialogue('foo', array('bar' => 'baz'), gzencode($xml));

        $result = $this->browser->get('foo', array('bar' => 'baz'));

        $this->assertInstanceOf('\Symfony\Component\DomCrawler\Crawler', $result);
        // objects are not identical, but their content should match
        $this->assertEquals($expected->html(), $result->html());
    }

    /**
     * Build client dialogue
     *
     * @param string $request
     * @param array $params
     * @param string|boolean $data
     */
    protected function buildDialogue($request, array $params, $data = false)
    {
        $req = $this->getMock('\Guzzle\Http\Message\RequestInterface');
        $response = $this
            ->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->api_prefix.
                (strpos($this->api_prefix, '?') !== false ? '&' : '?').
                http_build_query(array_merge(array(
                    'client'    => $this->api_client,
                    'clientver' => $this->api_clientver,
                    'protover'  => $this->api_protover,
                    'request'   => $request
                ), $params))
            )
            ->will($this->returnValue($req));
        $req
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));
        $response
            ->expects($this->once())
            ->method('isError')
            ->will($this->returnValue(!$data));
        if ($data) {
            $response
                ->expects($this->once())
                ->method('getBody')
                ->with(true)
                ->will($this->returnValue($data));
        }
    }
}