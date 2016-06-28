<?php
/**
 * AnimeDb package
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service;

use Symfony\Component\DomCrawler\Crawler;
use Guzzle\Http\Client;

/**
 * Browser
 *
 * @link http://anidb.net/
 * @link http://wiki.anidb.net/w/HTTP_API_Definition
 */
class Browser
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $api_prefix;

    /**
     * @var string
     */
    private $app_code;

    /**
     * @var string
     */
    private $image_prefix;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var CacheResponse
     */
    private $cache;

    /**
     * @param Client $client
     * @param string $host
     * @param string $api_prefix
     * @param string $api_client
     * @param string $api_clientver
     * @param string $api_protover
     * @param string $app_code
     * @param string $image_prefix
     */
    public function __construct(
        Client $client,
        $host,
        $api_prefix,
        $api_client,
        $api_clientver,
        $api_protover,
        $app_code,
        $image_prefix
    ) {
        $this->client = $client;
        $api_prefix .= strpos($api_prefix, '?') !== false ? '&' : '?';
        $api_prefix .= http_build_query([
            'client'    => $api_client,
            'clientver' => $api_clientver,
            'protover'  => $api_protover
        ]);
        $this->host = $host;
        $this->api_prefix = $api_prefix;
        $this->app_code = $app_code;
        $this->image_prefix = $image_prefix;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getApiHost()
    {
        return $this->client->getBaseUrl();
    }

    /**
     * @param int $timeout
     *
     * @return Browser
     */
    public function setTimeout($timeout)
    {
        $this->client->setDefaultOption('timeout', $timeout);

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return Browser
     */
    public function setProxy($proxy)
    {
        $this->client->setDefaultOption('proxy', $proxy);

        return $this;
    }

    /**
     * @param CacheResponse $cache
     */
    public function setResponseCache(CacheResponse $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @deprecated get() is deprecated since AniDbBrowser 2.0. Use getCrawler() instead
     *
     * @param string $request
     * @param array $params
     * @param bool $force
     *
     * @return Crawler
     */
    public function get($request, array $params = [], $force = false)
    {
        return $this->getCrawler($request, $params, $force);
    }

    /**
     * @param string $request
     * @param array $params
     * @param bool $force
     *
     * @return Crawler
     */
    public function getContent($request, array $params = [], $force = false)
    {
        $path = $this->api_prefix.'&request='.$request.($params ? '&'.http_build_query($params) : '');

        // try get response from cache
        if ($force || !($this->cache instanceof CacheResponse) || !($response = $this->cache->get($path))) {
            $response = $this->client->get($path)->setHeader('User-Agent', $this->app_code)->send();
            if ($response->isError()) {
                throw new \RuntimeException("Failed execute request '{$request}' to the server '".$this->getApiHost()."'");
            }
            $response = gzdecode($response->getBody(true));

            // cache response
            if ($this->cache instanceof CacheResponse) {
                $this->cache->set($request, $path, $response);
            }
        }

        return $response;
    }

    /**
     * @param string $request
     * @param array $params
     * @param bool $force
     *
     * @return Crawler
     */
    public function getCrawler($request, array $params = [], $force = false)
    {
        return new Crawler($this->getContent($request, $params, $force));
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function getImageUrl($image)
    {
        return $this->image_prefix.$image;
    }
}
