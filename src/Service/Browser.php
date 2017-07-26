<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;
use GuzzleHttp\Client as HttpClient;

class Browser
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var ResponseRepair
     */
    private $repair;

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
     * @param HttpClient     $client
     * @param ResponseRepair $repair
     * @param string         $api_host
     * @param string         $api_prefix
     * @param int            $api_protover
     * @param int            $app_version
     * @param string         $app_client
     * @param string         $app_code
     */
    public function __construct(
        HttpClient $client,
        ResponseRepair $repair,
        $api_host,
        $api_prefix,
        $api_protover,
        $app_version,
        $app_client,
        $app_code
    ) {
        $this->client = $client;
        $this->repair = $repair;
        $this->api_host = $api_host;
        $this->api_prefix = $api_prefix;
        $this->api_protover = $api_protover;
        $this->app_version = $app_version;
        $this->app_client = $app_client;
        $this->app_code = $app_code;
    }

    /**
     * @param string $request
     * @param array  $options
     *
     * @return string
     */
    public function get($request, array $options = [])
    {
        $options = $this->buildOptions($request, $options);
        $response = $this->client->request('GET', $this->api_host.$this->api_prefix, $options);
        $content = $this->repair->repair($response->getBody()->getContents());

        return $content;
    }

    /**
     * @param string $request
     * @param array  $options
     *
     * @return array
     */
    private function buildOptions($request, array $options = [])
    {
        $options['request'] = $request;
        $options['protover'] = $this->api_protover;
        $options['clientver'] = $this->app_version;
        $options['client'] = $this->app_client;
        $options['headers'] = array_merge(
            ['User-Agent' => $this->app_code],
            isset($options['headers']) ? $options['headers'] : []
        );

        return $options;
    }
}
