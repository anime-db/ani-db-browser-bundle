<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache;

/**
 * ExpireResolver.
 *
 * @see http://wiki.anidb.net/w/HTTP_API_Definition
 */
class ExpireResolver
{
    /**
     * @var array
     */
    protected $requests = [];

    /**
     * @var string
     */
    const DEFAULT_MODIFY = '+1 day';

    /**
     * @param array $requests
     */
    public function __construct(array $requests)
    {
        $this->requests = $requests;
    }

    /**
     * @param string $request
     * @param \DateTime $date
     *
     * @return \DateTime|null
     */
    public function getExpire($request, \DateTime $date)
    {
        if (!array_key_exists($request, $this->requests)) {
            return $date->modify(self::DEFAULT_MODIFY);
        }

        if ($this->requests[$request]) {
            return $date->modify($this->requests[$request]);
        }

        return null;
    }
}
