<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\DependencyInjection;

use AnimeDb\Bundle\AniDbBrowserBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\IntegerNode;
use Symfony\Component\Config\Definition\ScalarNode;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function testConfigTree()
    {
        $tree_builder = $this->configuration->getConfigTreeBuilder();

        $this->assertInstanceOf(TreeBuilder::class, $tree_builder);

        /* @var $tree ArrayNode */
        $tree = $tree_builder->buildTree();

        $this->assertInstanceOf(ArrayNode::class, $tree);
        $this->assertEquals('anime_db_ani_db_browser', $tree->getName());

        /* @var $children ScalarNode[] */
        $children = $tree->getChildren();

        $this->assertInternalType('array', $children);
        $this->assertEquals(['api', 'app'], array_keys($children));

        $this->assertInstanceOf(ArrayNode::class, $children['api']);

        /* @var $api ScalarNode[] */
        $api = $children['api']->getChildren();

        $this->assertInternalType('array', $api);
        $this->assertEquals(['host', 'prefix', 'protover'], array_keys($api));

        $this->assertInstanceOf(ScalarNode::class, $api['host']);
        $this->assertEquals('http://api.anidb.net:9001', $api['host']->getDefaultValue());
        $this->assertFalse($api['host']->isRequired());

        $this->assertInstanceOf(ScalarNode::class, $api['prefix']);
        $this->assertEquals('/httpapi/', $api['prefix']->getDefaultValue());
        $this->assertFalse($api['prefix']->isRequired());

        $this->assertInstanceOf(ScalarNode::class, $api['protover']);
        $this->assertEquals('1', $api['protover']->getDefaultValue());
        $this->assertFalse($api['protover']->isRequired());

        $this->assertInstanceOf(ArrayNode::class, $children['app']);

        /* @var $app ScalarNode[] */
        $app = $children['app']->getChildren();

        $this->assertInternalType('array', $app);
        $this->assertEquals(['version', 'client', 'code'], array_keys($app));

        $this->assertInstanceOf(ScalarNode::class, $app['version']);
        $this->assertFalse($app['version']->isRequired());

        $this->assertInstanceOf(ScalarNode::class, $app['client']);
        $this->assertFalse($app['client']->isRequired());

        $this->assertInstanceOf(ScalarNode::class, $app['code']);
        $this->assertFalse($app['code']->isRequired());
    }
}
