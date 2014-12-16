<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection\AnimeDbAniDbBrowserExtension;

/**
 * Test DependencyInjection
 *
 * @package AnimeDb\Bundle\AniDbBrowserBundle\Tests\DependencyInjection
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbAniDbBrowserExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test load
     */
    public function testLoad()
    {
        $di = new AnimeDbAniDbBrowserExtension();
        $di->load([], $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder'));
    }
}
