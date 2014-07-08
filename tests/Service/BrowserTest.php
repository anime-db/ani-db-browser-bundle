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
     * Crawler
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $crawler;

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

        $this->crawler = $this
            ->getMockBuilder('\Symfony\Component\DomCrawler\Crawler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->browser = new Browser(
            $this->client,
            $this->crawler,
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
}