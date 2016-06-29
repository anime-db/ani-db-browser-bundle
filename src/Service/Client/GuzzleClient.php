<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client;

use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use GuzzleHttp\Client;

class GuzzleClient implements ClientInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ResponseRepair
     */
    protected $repair;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param Client $client
     * @param ResponseRepair $repair
     * @param string $api_prefix
     * @param string $api_client
     * @param string $api_clientver
     * @param string $api_protover
     * @param string $app_code
     */
    public function __construct(
        Client $client,
        ResponseRepair $repair,
        $api_prefix,
        $api_client,
        $api_clientver,
        $api_protover,
        $app_code
    ) {
        $this->client = $client;
        $this->repair = $repair;

        $this->url = $api_prefix;
        $this->options = [
            'headers' => $app_code ? ['User-Agent' => $app_code] : [],
            'query' => [
                'client' => $api_client,
                'clientver' => $api_clientver,
                'protover' => $api_protover,
            ]
        ];
    }

    /**
     * @param int $timeout
     *
     * @return GuzzleClient
     */
    public function setTimeout($timeout)
    {
        $this->options['timeout'] = $timeout;

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return GuzzleClient
     */
    public function setProxy($proxy)
    {
        $this->options['proxy'] = $proxy;

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
        // add more query params
        $options = $this->options;
        $options['query'] = $options['query'] + ['request' => $request] + $params;

        $response = $this->client->request('GET', $this->url, $options);

        return $this->repair->repair(gzdecode($response->getBody())); // repair
    }
}
