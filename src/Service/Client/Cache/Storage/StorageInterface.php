<?php
/**
 * AnimeDb package
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\Storage;

interface StorageInterface
{
    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get($key);

    /**
     * @param string $key
     * @param string $data
     * @param \DateTime $expires
     */
    public function set($key, $data, \DateTime $expires);
}
