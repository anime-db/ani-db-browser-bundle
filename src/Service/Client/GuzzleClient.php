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
use Guzzle\Http\Client;

class GuzzleClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $api_prefix;

    /**
     * @var string
     */
    private $app_code;

    /**
     * @var ResponseRepair
     */
    private $response_repair;

    /**
     * @param Client $client
     * @param ResponseRepair $response_repair
     * @param string $api_prefix
     * @param string $api_client
     * @param string $api_clientver
     * @param string $api_protover
     * @param string $app_code
     */
    public function __construct(
        Client $client,
        ResponseRepair $response_repair,
        $api_prefix,
        $api_client,
        $api_clientver,
        $api_protover,
        $app_code
    ) {
        $this->client = $client;
        $this->app_code = $app_code;
        $this->response_repair = $response_repair;
        $this->api_prefix = $api_prefix.
            (strpos($api_prefix, '?') !== false ? '&' : '?').
            http_build_query([
                'client' => $api_client,
                'clientver' => $api_clientver,
                'protover' => $api_protover,
            ]);
    }

    /**
     * @param int $timeout
     *
     * @return GuzzleClient
     */
    public function setTimeout($timeout)
    {
        $this->client->setDefaultOption('timeout', $timeout);

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return GuzzleClient
     */
    public function setProxy($proxy)
    {
        $this->client->setDefaultOption('proxy', $proxy);

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
        $request = $this->client->get(
            $this->api_prefix.'&request='.$request.($params ? '&'.http_build_query($params) : '')
        );

        if ($this->app_code) {
            $request->setHeader('User-Agent', $this->app_code);
        }

        $response = $request->send();

        if ($response->isError()) {
            throw new \RuntimeException(sprintf(
                'Failed execute request "%s" to the server "%s"',
                $request,
                $this->client->getBaseUrl()
            ));
        }

        return $this->response_repair->repair(gzdecode($response->getBody(true))); // repair
    }
}
