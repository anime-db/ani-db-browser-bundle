<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service\Client;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\GuzzleClient;
use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use Guzzle\Http\Client;

class GuzzleClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResponseRepair
     */
    protected $repair;

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


    protected function setUp()
    {
        $this->client = $this->getMock('Guzzle\Http\Client');
        $this->repair = $this->getMock('AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair');
    }

    public function testSetTimeout()
    {
        $timeout = 123;
        $this->client
            ->expects($this->once())
            ->method('setDefaultOption')
            ->with('timeout', $timeout)
            ->will($this->returnSelf());

        $client = $this->buildGuzzleClient('', '');

        $this->assertEquals($client, $client->setTimeout($timeout));
    }

    public function testSetProxy()
    {
        $proxy = '127.0.0.1';
        $this->client
            ->expects($this->once())
            ->method('setDefaultOption')
            ->with('proxy', $proxy)
            ->will($this->returnSelf());

        $client = $this->buildGuzzleClient('', '');

        $this->assertEquals($client, $client->setProxy($proxy));
    }

    /**
     * @param $api_prefix
     * @param $app_code
     *
     * @return GuzzleClient
     */
    protected function buildGuzzleClient($api_prefix, $app_code)
    {
        return new GuzzleClient(
            $this->client,
            $this->repair,
            $api_prefix,
            $this->api_client,
            $this->api_clientver,
            $this->api_protover,
            $app_code
        );
    }

    /**
     * @param string $request
     * @param array $api_params
     * @param array $params
     *
     * @return string
     */
    protected function buildApiSuffix($request, array $api_params, array $params)
    {
        return
            [
                'client' => $this->api_client,
                'clientver' => $this->api_clientver,
                'protover' => $this->api_protover,
            ] +
            $api_params +
            [
                'request' => $request
            ] +
            $params;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        $params = [];

        $api_prefixes = [
            ['', '', []],
            ['/foo', '/foo', []],
            ['?foo=bar', '', ['foo' => 'bar']],
            ['/foo?bar=baz', '/foo', ['bar' => 'baz']],
        ];

        $request_params = [
            [],
            ['foo' => 123],
            ['baz' => 456],
        ];

        foreach (['', 'my_app_code'] as $app_code) {
            foreach ($request_params as $request_param) {
                foreach ($api_prefixes as $api_prefix) {
                    $params[] = [
                        $api_prefix[0],
                        $api_prefix[1],
                        $app_code,
                        $request_param,
                        $api_prefix[2] + $request_param,
                        $app_code ? ['User-Agent' => $app_code] : []
                    ];
                }
            }
        }

        return $params;
    }

    /**
     * @dataProvider getRequestParams
     *
     * @expectedException \RuntimeException
     *
     * @param string $api_prefix
     * @param string $final_api_prefix
     * @param string $app_code
     * @param array $params
     * @param array $query
     * @param array $headers
     */
    public function testGetError($api_prefix, $final_api_prefix, $app_code, array $params, array $query, array $headers)
    {
        $request = 'my_request';
        $host = 'my_host';

        $response_obj = $this
            ->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $response_obj
            ->expects($this->once())
            ->method('isError')
            ->will($this->returnValue(true));

        $request_obj = $this
            ->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request_obj
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response_obj));

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($final_api_prefix, null, [
                'query' => $this->buildApiSuffix($request, $query, $params),
                'headers' => $headers,
            ])
            ->will($this->returnValue($request_obj));
        $this->client
            ->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue($host));

        $this->buildGuzzleClient($api_prefix, $app_code)->get($request, $params);
    }

    /**
     * @dataProvider getRequestParams
     *
     * @param string $api_prefix
     * @param string $final_api_prefix
     * @param string $app_code
     * @param array $params
     * @param array $query
     * @param array $headers
     */
    public function testGet($api_prefix, $final_api_prefix, $app_code, array $params, array $query, array $headers)
    {
        $request = 'my_request';
        $body = 'my_body';
        $body_repair = 'my_body_repair';

        $response_obj = $this
            ->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $response_obj
            ->expects($this->once())
            ->method('isError')
            ->will($this->returnValue(false));
        $response_obj
            ->expects($this->once())
            ->method('getBody')
            ->with(true)
            ->will($this->returnValue(gzencode($body)));

        $request_obj = $this
            ->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request_obj
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response_obj));

        $this->client
            ->expects($this->once())
            ->method('get')
            ->with($final_api_prefix, null, [
                'query' => $this->buildApiSuffix($request, $query, $params),
                'headers' => $headers,
            ])
            ->will($this->returnValue($request_obj));

        $this->repair
            ->expects($this->once())
            ->method('repair')
            ->with($body)
            ->will($this->returnValue($body_repair));

        $this->assertEquals($body_repair, $this->buildGuzzleClient($api_prefix, $app_code)->get($request, $params));
    }
}
