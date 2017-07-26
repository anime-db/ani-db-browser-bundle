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
        if (!preg_match('/<body><error>([^<>]+)<\/error><\/body>/im', $response, $match)) {
            return $response;
        }

        switch ($match[1]) {
            case 'Banned':
                throw BannedException::banned();
                break;
            case 'Anime not found':
                throw NotFoundException::anime();
                break;
            default:
                throw ErrorException::error($match[1]);
                break;
        }
    }
}
