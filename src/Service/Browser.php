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
     * API path prefix
     *
     * @var string
     */
    private $api_prefix;

    /**
     * App code
     *
     * @var string
     */
    private $app_code;

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
     * Crawler
     *
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    private $crawler;

    /**
     * Construct
     *
     * @param \Guzzle\Http\Client $client
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string $host
     * @param string $api_prefix
     * @param string $api_client
     * @param string $api_clientver
     * @param string $api_protover
     * @param string $image_prefix
     */
    public function __construct(
        Client $client,
        Crawler $crawler,
        $host,
        $api_prefix,
        $api_client,
        $api_clientver,
        $api_protover,
        $image_prefix
    ) {
        $this->client = $client;
        $this->crawler = $crawler;
        $api_prefix .= strpos($api_prefix, '?') !== false ? '&' : '?';
        $api_prefix .= http_build_query([
            'client'    => $api_client,
            'clientver' => $api_clientver,
            'protover'  => $api_protover
        ]);
        $this->host = $host;
        $this->api_prefix = $api_prefix;
        $this->image_prefix = $image_prefix;
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
        return $this->client->getBaseUrl();
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
        $body = gzdecode($response->getBody(true));
        $body = mb_convert_encoding($body, 'html-entities', 'utf-8');
        $crawler = clone $this->crawler;
        return $crawler->add($body);
    }

    /**
     * Get image URL
     *
     * @param string $iamge
     *
     * @return string
     */
    public function getImageUrl($image)
    {
        return $this->image_prefix.$image;
    }
}