<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Browser.
 *
 * @link http://anidb.net/
 * @link http://wiki.anidb.net/w/HTTP_API_Definition
 */
class Browser
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $api_host;

    /**
     * @var string
     */
    protected $image_prefix;

    /**
     * @param ClientInterface $client
     * @param string $host
     * @param string $api_host
     * @param string $image_prefix
     */
    public function __construct(ClientInterface $client, $host, $api_host, $image_prefix)
    {
        $this->client = $client;
        $this->host = $host;
        $this->api_host = $api_host;
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
        return $this->api_host;
    }

    /**
     * @param int $timeout
     *
     * @return Browser
     */
    public function setTimeout($timeout)
    {
        $this->client->setTimeout($timeout);

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return Browser
     */
    public function setProxy($proxy)
    {
        $this->client->setProxy($proxy);

        return $this;
    }
    /**
     * @deprecated get() is deprecated since AniDbBrowser 2.0. Use getCrawler() instead
     * @codeCoverageIgnore
     *
     * @param string $request
     * @param array $params
     * @param bool $force
     *
     * @return Crawler
     */
    public function get($request, array $params = [], $force = false)
    {
        trigger_error('get() is deprecated since AniDbBrowser 2.0. Use getCrawler() instead', E_USER_DEPRECATED);
        return $this->getCrawler($request, $params);
    }

    /**
     * @param string $request
     * @param array $params
     *
     * @return string
     */
    public function getContent($request, array $params = [])
    {
        return $this->client->get($request, $params);
    }

    /**
     * @param string $request
     * @param array $params
     *
     * @return Crawler
     */
    public function getCrawler($request, array $params = [])
    {
        return new Crawler($this->getContent($request, $params));
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
