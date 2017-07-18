<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\Storage;

use Symfony\Component\Filesystem\Filesystem;

class FileStorage implements StorageInterface
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $cache_dir = '';

    /**
     * @param Filesystem $fs
     * @param string     $cache_dir
     */
    public function __construct(Filesystem $fs, $cache_dir)
    {
        $this->fs = $fs;
        $this->cache_dir = $cache_dir;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        $filename = $this->getFilename($key);
        if ($this->fs->exists($filename) && filemtime($filename) >= time()) {
            return file_get_contents($filename);
        }

        return null;
    }

    /**
     * @param string    $key
     * @param string    $data
     * @param \DateTime $expires
     */
    public function set($key, $data, \DateTime $expires)
    {
        $filename = $this->getFilename($key);
        $this->fs->dumpFile($filename, $data);
        $this->fs->touch($filename, $expires->getTimestamp());
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getFilename($key)
    {
        return $this->cache_dir.md5($key).'.xml';
    }
}
