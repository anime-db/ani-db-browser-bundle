<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service\Client\Cache\Storage;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\Storage\FileStorage;
use Symfony\Component\Filesystem\Filesystem;

class FileStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var FileStorage
     */
    private $storage;

    /**
     * @var string
     */
    private $dir = '';

    /**
     * @var string
     */
    private $filename = '';

    private $key = 'foo';

    private $data = 'bar';

    protected function setUp()
    {
        $this->fs = new Filesystem();
        $this->dir = sys_get_temp_dir().'/unit-test/';
        $this->filename = $this->dir.md5($this->key).'.xml';
        $this->fs->mkdir($this->dir, 0755);

        $this->storage = new FileStorage($this->fs, $this->dir);
    }

    public function tearDown()
    {
        $this->fs->remove($this->dir);
    }

    public function testGetNoDir()
    {
        $this->fs->remove($this->dir);
        $this->assertNull($this->storage->get($this->key));
    }

    public function testGetNoFile()
    {
        $this->assertNull($this->storage->get($this->key));
    }

    public function testGetExpired()
    {
        $this->fs->dumpFile($this->filename, $this->data);
        $this->fs->touch($this->filename, time() - 60);
        $this->assertNull($this->storage->get($this->key));
    }

    public function testGet()
    {
        $this->fs->dumpFile($this->filename, $this->data);
        $this->assertEquals($this->data, $this->storage->get($this->key));
    }

    public function testSet()
    {
        $date = new \DateTime('+1 day');
        $this->storage->set($this->key, $this->data, $date);

        $this->assertEquals($date->getTimestamp(), filemtime($this->filename));
        $this->assertEquals($this->data, $this->storage->get($this->key));
    }
}
