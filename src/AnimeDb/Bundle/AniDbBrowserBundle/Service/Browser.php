<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Guzzle\Http\Client;

/**
 * Browser
 *
 * @link http://anidb.net/
 * @link http://wiki.anidb.net/w/HTTP_API_Definition
 * @package AnimeDb\Bundle\AniDbBrowserBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Browser
{
    /**
     * Host
     *
     * @var string
     */
    private $host;

    /**
     * API host
     *
     * @var string
     */
    private $api_host;

    /**
     * API path prefix
     *
     * @var string
     */
    private $api_prefix;

    /**
     * Image URL prefix
     *
     * @var string
     */
    private $image_prefix;

    /**
     * HTTP client
     *
     * @var \Guzzle\Http\Client
     */
    private $client;

    /**
     * Construct
     *
     * @param string $host
     * @param string $api_host
     * @param string $api_prefix
     * @param string $api_client
     * @param string $api_clientver
     * @param string $api_protover
     * @param string $image_prefix
     */
    public function __construct(
        $host,
        $api_host,
        $api_prefix,
        $api_client,
        $api_clientver,
        $api_protover,
        $image_prefix
    ) {
        $api_prefix .= strpos($api_prefix, '?') !== false ? '&' : '?';
        $api_prefix .= http_build_query([
            'client'    => $api_client,
            'clientver' => $api_clientver,
            'protover'  => $api_protover
        ]);
        $this->host = $host;
        $this->api_host = $api_host;
        $this->api_prefix = $api_prefix;
        $this->image_prefix = $image_prefix;
    }

    /**
     * Get HTTP client
     *
     * @param \Guzzle\Http\Client
     */
    protected function getClient()
    {
        if (!($this->client instanceof Client)) {
            $this->client = new Client($this->api_host);
        }
        return $this->client;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get API host
     *
     * @return string
     */
    public function getApiHost()
    {
        return $this->api_host;
    }

    /**
     * Get data
     *
     * @param string $request
     * @param array $params
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function get($request, array $params = [])
    {
        $path = $this->api_prefix.'&request='.$request.($params ? '&'.http_build_query($params) : '');

        /* @var $response \Guzzle\Http\Message\Response */
        $response = $this->getClient()->get($path)->send();
        if ($response->isError()) {
            throw new \RuntimeException("Failed execute request '{$request}' to the server '{$this->api_host}'");
        }
        return new Crawler(gzdecode($response->getBody(true)));
    }

    /**
     * Get image URL
     *
     * @param string $iamge
     *
     * @return string
     */
    public function getImageUrl($iamge)
    {
        return $this->image_prefix.$iamge;
    }
}