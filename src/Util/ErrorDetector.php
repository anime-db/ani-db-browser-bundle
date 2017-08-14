<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Util;

use AnimeDb\Bundle\AniDbBrowserBundle\Exception\BannedException;
use AnimeDb\Bundle\AniDbBrowserBundle\Exception\NotFoundException;
use AnimeDb\Bundle\AniDbBrowserBundle\Exception\ErrorException;

class ErrorDetector
{
    /**
     * @param string $response
     *
     * @return string
     */
    public function detect($response)
    {
        if (stripos($response, '<error>') === false) {
            return $response;
        }

        $error = preg_replace('/<error>([^<]+)<\/error>/im', '$1', $response);

        switch ($error) {
            case 'Banned':
                throw BannedException::banned();
            case 'Anime not found':
                throw NotFoundException::anime();
            default:
                throw ErrorException::error($error);
        }
    }
}
