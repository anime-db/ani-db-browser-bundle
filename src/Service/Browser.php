<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Util\ErrorDetector;
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
     * @var ErrorDetector
     */
    private $detector;

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
     * @param ErrorDetector  $detector
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
        ErrorDetector $detector,
        $api_host,
        $api_prefix,
        $api_protover,
        $app_version,
        $app_client,
        $app_code
    ) {
        $this->client = $client;
        $this->repair = $repair;
        $this->detector = $detector;
        $this->api_host = $api_host;
        $this->api_prefix = $api_prefix;
        $this->api_protover = $api_protover;
        $this->app_version = $app_version;
        $this->app_client = $app_client;
        $this->app_code = $app_code;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function get(array $options)
    {
        $options = $this->options($options);

        $response = $this->client->request('GET', $this->api_host.$this->api_prefix, $options);
        $content = $response->getBody()->getContents();

        $content = $this->repair->repair($content);
        $content = $this->detector->detect($content);

        return $content;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function options(array $options = [])
    {
        $options['query'] = array_merge(
            [
                'protover' => $this->api_protover,
                'clientver' => $this->app_version,
                'client' => $this->app_client,
            ],
            isset($options['query']) ? $options['query'] : []
        );

        $options['headers'] = array_merge(
            [
                'User-Agent' => $this->app_code,
            ],
            isset($options['headers']) ? $options['headers'] : []
        );

        return $options;
    }
}
