<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Exception;

class NotFoundException extends ErrorException
{
    /**
     * @return NotFoundException
     */
    public static function anime()
    {
        return new self('Anime not found.');
    }
}
