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
     * @return array
     */
    public function responses()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;

        return [
            [$xml.'<body><error>%s</error></body>'],
            [$xml.'<error>%s</error>'],
        ];
    }

    /**
     * @expectedException \AnimeDb\Bundle\AniDbBrowserBundle\Exception\BannedException
     * @dataProvider responses
     *
     * @param string $response
     */
    public function testBanned($response)
    {
        $this->checker->detect(sprintf($response, 'Banned'));
    }

    /**
     * @expectedException \AnimeDb\Bundle\AniDbBrowserBundle\Exception\NotFoundException
     * @dataProvider responses
     *
     * @param string $response
     */
    public function testNotFound($response)
    {
        $this->checker->detect(sprintf($response, 'Anime not found'));
    }

    /**
     * @expectedException \AnimeDb\Bundle\AniDbBrowserBundle\Exception\ErrorException
     * @expectedExceptionMessage Foo
     * @dataProvider responses
     *
     * @param string $response
     */
    public function testError($response)
    {
        $this->checker->detect(sprintf($response, 'Foo'));
    }
}
