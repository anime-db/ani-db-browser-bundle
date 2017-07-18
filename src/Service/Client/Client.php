<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client;

interface Client
{
    /**
     * @param int $timeout
     *
     * @return Client
     */
    public function setTimeout($timeout);

    /**
     * @param string $proxy
     *
     * @return Client
     */
    public function setProxy($proxy);

    /**
     * @param string $request
     * @param array  $params
     *
     * @return string
     */
    public function get($request, array $params = []);
}
