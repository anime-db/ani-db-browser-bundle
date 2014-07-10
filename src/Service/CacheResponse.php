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

/**
 * Cache response
 *
 * @link http://wiki.anidb.net/w/HTTP_API_Definition
 * @package AnimeDb\Bundle\AniDbBrowserBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class CacheResponse
{
    /**
     * Cache dir
     *
     * @var string
     */
    protected $cache_dir;

    /**
     * Construct
     *
     * @param string $cache_dir
     */
    public function __construct($cache_dir)
    {
        $this->cache_dir = $cache_dir;
    }

    /**
     * Get response data
     *
     * @param string $url
     *
     * @return string|null
     */
    public function get($url)
    {
        if (is_dir($this->cache_dir)) {
            $filename = $this->getFilename($url);
            if (file_exists($filename) && filemtime($filename) >= time()) {
                return file_get_contents($filename);
            }
        }
        return null;
    }

    /**
     * Set response data
     *
     * @param string $request
     * @param string $url
     * @param string $response
     */
    public function set($request, $url, $response)
    {
        if ($expires = $this->getRequestExpires($request)) {
            if (!is_dir($this->cache_dir)) {
                mkdir($this->cache_dir, 0755, true);
            }
            $filename = $this->getFilename($url);
            file_put_contents($filename, $response);
            touch($filename, $expires);
        }
    }

    /**
     * Get filename
     *
     * @param string $url
     *
     * @return string
     */
    protected function getFilename($url)
    {
        return $this->cache_dir.md5($url).'.xml';
    }

    /**
     * Get cache request expires
     *
     * @link http://wiki.anidb.net/w/HTTP_API_Definition
     *
     * @param string $request
     *
     * @return integer
     */
    protected function getRequestExpires($request)
    {
        switch ($request) {
            case 'anime':
                return strtotime('+1 week');
            case 'categorylist':
                return strtotime('+6 month');
            case 'randomrecommendation':
            case 'randomsimilar':
            case 'mylistsummary':
                return 0; // no cache
            case 'hotanime':
            case 'main':
            default:
                return strtotime('+1 day');
        }
    }
}