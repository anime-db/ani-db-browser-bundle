<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Browser;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $host = 'host';

    /**
     * @var string
     */
    private $api_host = 'api_host';

    /**
     * @var string
     */
    private $image_prefix = 'image_prefix';

    /**
     * @var string
     */
    private $xml = '<?xml version="1.0"?><root><text>Hello, world!</text></root>';

    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ClientInterface
     */
    private $client;

    protected function setUp()
    {
        $this->client = $this
            ->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->browser = new Browser($this->client, $this->host, $this->api_host, $this->image_prefix);
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
        $this->assertEquals($this->api_host, $this->browser->getApiHost());
    }

    public function testSetTimeout()
    {
        $timeout = 123;
        $this->client
            ->expects($this->once())
            ->method('setTimeout')
            ->with($timeout)
        ;

        $this->assertEquals($this->browser, $this->browser->setTimeout($timeout));
    }

    public function testSetProxy()
    {
        $proxy = '127.0.0.1';
        $this->client
            ->expects($this->once())
            ->method('setProxy')
            ->with($proxy)
        ;

        $this->assertEquals($this->browser, $this->browser->setProxy($proxy));
    }

    public function testGetContent()
    {
        $request = 'foo';
        $params = ['bar' => 'baz'];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($request, $params)
            ->will($this->returnValue($this->xml))
        ;

        $this->assertEquals($this->xml, $this->browser->getContent($request, $params));
    }

    public function testGetCrawler()
    {
        $request = 'foo';
        $params = ['bar' => 'baz'];

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($request, $params)
            ->will($this->returnValue($this->xml))
        ;

        $result = $this->browser->getCrawler($request, $params);

        $this->assertInstanceOf(Crawler::class, $result);

        // objects are not identical, but their content should match
        $this->assertEquals((new Crawler($this->xml))->html(), $result->html());
    }
}
