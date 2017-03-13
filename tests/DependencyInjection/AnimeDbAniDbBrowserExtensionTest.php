<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection\AnimeDbAniDbBrowserExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AnimeDbAniDbBrowserExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test load.
     */
    public function testLoad()
    {
        /* @var $builder \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $di = new AnimeDbAniDbBrowserExtension();
        $di->load([], $builder);
    }
}
