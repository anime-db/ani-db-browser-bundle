<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\CacheResponse;
use Symfony\Component\Filesystem\Filesystem;

class CacheResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var CacheResponse
     */
    protected $cache;

    public function setUp()
    {
        $this->fs = new Filesystem();
        $this->dir = sys_get_temp_dir().'/unit-test/';
        $this->filename = $this->dir.md5('foo').'.xml';
        $this->cache = new CacheResponse($this->fs, $this->dir);
        $this->fs->mkdir($this->dir, 0755);
    }

    public function tearDown()
    {
        $this->fs->remove($this->dir);
    }

    public function testGetNoDir()
    {
        $this->tearDown(); // remove dir
        $this->assertNull($this->cache->get('foo'));
    }

    public function testGetNoFile()
    {
        $this->assertNull($this->cache->get('foo'));
    }

    public function testGetExpired()
    {
        $this->fs->dumpFile($this->filename, '');
        $this->fs->touch($this->filename, time() - 60);
        $this->assertNull($this->cache->get('foo'));
    }

    public function testGet()
    {
        $this->fs->dumpFile($this->filename, 'bar');
        $this->assertEquals('bar', $this->cache->get('foo'));
    }

    public function testSetNoCache()
    {
        $requests = [
            'randomrecommendation',
            'randomsimilar',
            'mylistsummary',
        ];
        foreach ($requests as $request) {
            $this->cache->set($request, 'foo', 'bar');
            $this->assertFalse($this->fs->exists($this->filename));
        }
    }

    public function testSet()
    {
        $this->tearDown(); // remove dir
        $requests = [
            'anime' => '+1 week',
            'categorylist' => '+6 month',
            'hotanime' => '+1 day',
            'main' => '+1 day',
            'baz' => '+1 day',
        ];

        foreach ($requests as $request => $expires) {
            $this->cache->set($request, 'foo', 'bar');
            $this->assertEquals(strtotime($expires), filemtime($this->filename));
            $this->assertEquals('bar', $this->cache->get('foo'));
        }
    }
}
