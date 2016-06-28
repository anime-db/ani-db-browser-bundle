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
    protected $client;

    /**
     * @var ResponseRepair
     */
    protected $repair;

    /**
     * @var string
     */
    protected $api_prefix;

    /**
     * @var string
     */
    protected $app_code;

    /**
     * @var array
     */
    protected $request_params = [];

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
        $this->app_code = $app_code;
        $this->repair = $repair;

        $query = [];
        if ($api_prefix) {
            parse_str((string)parse_url($api_prefix, PHP_URL_QUERY), $query);
            $this->api_prefix = (string)parse_url($api_prefix, PHP_URL_PATH);
        }
        $this->request_params = [
            'client' => $api_client,
            'clientver' => $api_clientver,
            'protover' => $api_protover,
        ] + $query;
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
            $this->api_prefix,
            $this->app_code ? ['User-Agent' => $this->app_code] : [],
            ['query' => $this->request_params + ['request' => $request] + $params]
        );

        $response = $request->send();

        if ($response->isError()) {
            throw new \RuntimeException(sprintf(
                'Failed execute request "%s" to the server "%s"',
                $request,
                $this->client->getBaseUrl()
            ));
        }

        return $this->repair->repair(gzdecode($response->getBody(true))); // repair
    }
}
