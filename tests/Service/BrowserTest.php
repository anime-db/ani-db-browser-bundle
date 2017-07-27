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
    private $api_host = 'my_api_host';

    /**
     * @var string
     */
    private $api_prefix = 'my_api_prefix';

    /**
     * @var int
     */
    private $api_protover = 'my_api_protover';

    /**
     * @var int
     */
    private $app_version = 'my_app_version';

    /**
     * @var string
     */
    private $app_client = 'my_app_client';

    /**
     * @var string
     */
    private $app_code = 'my_app_code';

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
    public function override()
    {
        return [
            [
                '',
                ['timeout' => 5]
            ],
            [
                'Override User Agent', // try override app code
                [
                    'query' => [
                        'foo' => 123,
                        'client' => 'bar', // try override client name
                    ],
                ]
            ],
        ];
    }

    /**
     * @dataProvider override
     *
     * @param string $app_code
     * @param array  $params
     */
    public function testGet($app_code, array $params)
    {
        $options = [
            'query' => [
                'protover' => $this->api_protover,
                'clientver' => $this->app_version,
                'client' => $this->app_client,
            ],
            'headers' => [
                'User-Agent' => $this->app_code,
            ],
        ];

        if ($app_code) {
            $options['headers']['User-Agent'] = $app_code;
            $params['headers']['User-Agent'] = $app_code;
        }

        foreach ($params as $key => $param) {
            if (is_array($param) && is_array($options[$key])) {
                $options[$key] = array_merge($options[$key], $param);
            } else {
                $options[$key] = $param;
            }
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

        $this->assertEquals($repair, $this->browser->get($params));
    }
}
