<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Guzzle\RequestConfigurator;
use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use GuzzleHttp\Client;

class GuzzleClient implements ClientInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestConfigurator
     */
    protected $configurator;

    /**
     * @var ResponseRepair
     */
    protected $repair;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param Client $client
     * @param RequestConfigurator $configurator
     * @param ResponseRepair $repair
     * @param string $api_prefix
     */
    public function __construct(
        Client $client,
        RequestConfigurator $configurator,
        ResponseRepair $repair,
        $api_prefix
    ) {
        $this->url = $api_prefix;
        $this->client = $client;
        $this->repair = $repair;
        $this->configurator = $configurator;
    }

    /**
     * @param int $timeout
     *
     * @return GuzzleClient
     */
    public function setTimeout($timeout)
    {
        $this->configurator->setTimeout($timeout);

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return GuzzleClient
     */
    public function setProxy($proxy)
    {
        $this->configurator->setProxy($proxy);

        return $this;
    }

    /**
     * @param string $request
     * @param array $params
     *
     * @return string
     */
    public function get($request, array $params = [])
    {
        $options = $this->configurator->withRequest($request, $params)->getOptions();
        $response = $this->client->request('GET', $this->url, $options);

        return $this->repair->repair(gzdecode($response->getBody())); // repair
    }
}
