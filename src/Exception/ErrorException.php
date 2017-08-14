<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Exception;

class ErrorException extends \RuntimeException
{
    /**
     * @param string $message
     *
     * @return ErrorException
     */
    public static function error($message)
    {
        return new self($message);
    }
}
