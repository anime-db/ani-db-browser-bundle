<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Cache response.
 *
 * @link http://wiki.anidb.net/w/HTTP_API_Definition
 */
class CacheResponse
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $cache_dir;

    /**
     * @param Filesystem $fs
     * @param string $cache_dir
     */
    public function __construct(Filesystem $fs, $cache_dir)
    {
        $this->fs = $fs;
        $this->cache_dir = $cache_dir;
    }

    /**
     * @param string $url
     *
     * @return string|null
     */
    public function get($url)
    {
        $filename = $this->getFilename($url);
        if ($this->fs->exists($filename) && filemtime($filename) >= time()) {
            return file_get_contents($filename);
        }

        return null;
    }

    /**
     * @param string $request
     * @param string $url
     * @param string $response
     */
    public function set($request, $url, $response)
    {
        if ($expires = $this->getRequestExpires($request)) {
            $filename = $this->getFilename($url);
            $this->fs->dumpFile($filename, $response);
            $this->fs->touch($filename, $expires);
        }
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function getFilename($url)
    {
        return $this->cache_dir.md5($url).'.xml';
    }

    /**
     * Get cache request expires.
     *
     * @link http://wiki.anidb.net/w/HTTP_API_Definition
     *
     * @param string $request
     *
     * @return int
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
