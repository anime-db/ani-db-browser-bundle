<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service\Client;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Guzzle\RequestConfigurator;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\GuzzleClient;
use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class GuzzleClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected $guzzle;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestConfigurator
     */
    protected $configurator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResponseRepair
     */
    protected $repair;

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $api_prefix = '/foo/bar';

    protected function setUp()
    {
        $this->guzzle = $this->getMock(Client::class);
        $this->configurator = $this->getMock(RequestConfigurator::class);
        $this->repair = $this->getMock(ResponseRepair::class);

        $this->client = new GuzzleClient($this->guzzle, $this->configurator, $this->repair, $this->api_prefix);
    }

    public function testSetTimeout()
    {
        $timeout = 123;
        $this->configurator
            ->expects($this->once())
            ->method('setTimeout')
            ->with($timeout)
            ->will($this->returnSelf());

        $this->assertEquals($this->client, $this->client->setTimeout($timeout));
    }

    public function testSetProxy()
    {
        $proxy = '127.0.0.1';
        $this->configurator
            ->expects($this->once())
            ->method('setProxy')
            ->with($proxy)
            ->will($this->returnSelf());

        $this->assertEquals($this->client, $this->client->setProxy($proxy));
    }

    public function testGet()
    {
        $request = 'my_request';
        $body = 'my_body';
        $body_repair = 'my_body_repair';
        $params = ['foo' => 123];
        $options = ['bar' => 456];

        $response = $this
            ->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(gzencode($body)));

        $this->guzzle
            ->expects($this->once())
            ->method('request')
            ->with('GET', $this->api_prefix, $options)
            ->will($this->returnValue($response));

        $this->configurator
            ->expects($this->once())
            ->method('withRequest')
            ->with($request, $params)
            ->will($this->returnSelf());
        $this->configurator
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($options));

        $this->repair
            ->expects($this->once())
            ->method('repair')
            ->with($body)
            ->will($this->returnValue($body_repair));

        $this->assertEquals($body_repair, $this->client->get($request, $params));
    }
}
