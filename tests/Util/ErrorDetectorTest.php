<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Util;

use AnimeDb\Bundle\AniDbBrowserBundle\Util\ErrorDetector;

class ErrorDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ErrorDetector
     */
    private $checker;

    protected function setUp()
    {
        $this->checker = new ErrorDetector();
    }

    public function testNoErrors()
    {
        $response = 'foo';

        $this->assertEquals($response, $this->checker->detect($response));
    }

    /**
     * @expectedException \AnimeDb\Bundle\AniDbBrowserBundle\Exception\BannedException
     */
    public function testBanned()
    {
        $response = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $response .= '<body><error>Banned</error></body>';

        $this->checker->detect($response);
    }

    /**
     * @expectedException \AnimeDb\Bundle\AniDbBrowserBundle\Exception\NotFoundException
     */
    public function testNotFound()
    {
        $response = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $response .= '<body><error>Anime not found</error></body>';

        $this->checker->detect($response);
    }

    /**
     * @expectedException \AnimeDb\Bundle\AniDbBrowserBundle\Exception\ErrorException
     * @expectedExceptionMessage Foo
     */
    public function testError()
    {
        $response = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $response .= '<body><error>Foo</error></body>';

        $this->checker->detect($response);
    }
}
