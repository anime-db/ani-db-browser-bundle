<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Util;

class ResponseRepair
{
    /**
     * @param string $content
     *
     * @return string
     */
    public function repair($content)
    {
        return str_replace("\xf0\x9d\x84\x87", '', $content); // \u1D107
    }
}
