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
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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
        $this->client = $this->getMock(Client::class);
        $this->repair = $this->getMock(ResponseRepair::class);
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
     * @param array $params
     *
     * @return string
     */
    protected function buildApiSuffix($request, array $params)
    {
        return
            [
                'client' => $this->api_client,
                'clientver' => $this->api_clientver,
                'protover' => $this->api_protover,
            ] +
            [
                'request' => $request,
            ] +
            $params;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        $params = [];

        $request_params = [
            [[], []],
            [['foo' => 123], ['foo' => 123]],
            [['foo' => 123, 'request' => 456, 'client' => 789], ['foo' => 123]],
        ];

        foreach ([0, 123] as $timeout) {
            foreach (['', '127.0.0.1'] as $proxy) {
                foreach (['', 'my_app_code'] as $app_code) {
                    foreach ($request_params as $request_param) {
                        $params[] = [
                            '/foo',
                            $app_code,
                            $request_param[0],
                            $request_param[1],
                            $app_code ? ['User-Agent' => $app_code] : [],
                            $proxy,
                            $timeout
                        ];
                    }
                }
            }
        }

        return $params;
    }

    /**
     * @dataProvider getRequestParams
     *
     * @param string $api_prefix
     * @param string $app_code
     * @param array $params
     * @param array $query
     * @param array $headers
     * @param string $proxy
     * @param int $timeout
     */
    public function testGet($api_prefix, $app_code, array $params, array $query, array $headers, $proxy, $timeout)
    {
        $request = 'my_request';
        $body = 'my_body';
        $body_repair = 'my_body_repair';
        $options = [
            'headers' => $headers,
            'query' => $this->buildApiSuffix($request, $query)
        ];

        $client = $this->buildGuzzleClient($api_prefix, $app_code);

        if ($proxy) {
            $client->setProxy($proxy);
            $options['proxy'] = $proxy;
        }

        if ($timeout) {
            $client->setTimeout($timeout);
            $options['timeout'] = $timeout;
        }

        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(gzencode($body)));

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', $api_prefix, $options)
            ->will($this->returnValue($response));

        $this->repair
            ->expects($this->once())
            ->method('repair')
            ->with($body)
            ->will($this->returnValue($body_repair));

        $this->assertEquals($body_repair, $client->get($request, $params));
    }
}
