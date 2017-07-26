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
use AnimeDb\Bundle\AniDbBrowserBundle\Util\ErrorDetector;
use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $api_host;

    /**
     * @var string
     */
    private $api_prefix;

    /**
     * @var int
     */
    private $api_protover;

    /**
     * @var int
     */
    private $app_version;

    /**
     * @var string
     */
    private $app_client;

    /**
     * @var string
     */
    private $app_code;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|HttpClient
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResponseRepair
     */
    private $repair;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ErrorDetector
     */
    private $detector;

    /**
     * @var Browser
     */
    private $browser;

    protected function setUp()
    {
        $this->client = $this->getMock(HttpClient::class);
        $this->repair = $this->getMock(ResponseRepair::class);
        $this->detector = $this->getMock(ErrorDetector::class);

        $this->browser = new Browser(
            $this->client,
            $this->repair,
            $this->detector,
            $this->api_host,
            $this->api_prefix,
            $this->api_protover,
            $this->app_version,
            $this->app_client,
            $this->app_code
        );
    }

    /**
     * @return array
     */
    public function appClients()
    {
        return [
            [''],
            ['Override User Agent'],
        ];
    }

    /**
     * @dataProvider appClients
     *
     * @param string $app_client
     */
    public function testGet($app_client)
    {
        $request = 'foo';
        $params = ['bar' => 'baz'];
        $options = $params + [
            'request' => $request,
            'protover' => $this->api_protover,
            'clientver' => $this->app_version,
            'client' => $this->app_client,
            'headers' => [
                'User-Agent' => $this->app_code,
            ],
        ];

        if ($app_client) {
            $options['headers']['User-Agent'] = $app_client;
            $params['headers']['User-Agent'] = $app_client;
        }

        $xml = '<?xml version="1.0"?><root><text>Hello, world!</text></root>';
        $repair = 'foo';

        $stream = $this->getMock(StreamInterface::class);
        $stream
            ->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($xml))
        ;

        $message = $this->getMock(MessageInterface::class);
        $message
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($stream))
        ;

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', $this->api_host.$this->api_prefix, $options)
            ->will($this->returnValue($message))
        ;

        $this->repair
            ->expects($this->once())
            ->method('repair')
            ->with($xml)
            ->will($this->returnValue($repair))
        ;
        $this->detector
            ->expects($this->once())
            ->method('detect')
            ->with($repair)
            ->will($this->returnValue($repair))
        ;

        $this->assertEquals($repair, $this->browser->get($request, $params));
    }
}
