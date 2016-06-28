<?php
/**
 * AnimeDb package
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Util;

use AnimeDb\Bundle\AniDbBrowserBundle\Util\ResponseRepair;

class ResponseRepairTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getResponses()
    {
        $params = [
            ['foo', 'foo'],
            ['foo', '𝄇foo'],
            ['foo', "\xf0\x9d\x84\x87foo"],
            ['foo', mb_convert_encoding('&#119047;', 'UTF-8', 'HTML-ENTITIES').'foo'],
            ['foo', html_entity_decode('&#119047;', 0, 'UTF-8').'foo'],
        ];

        // PHP 7.0.0 has introduced the "Unicode codepoint escape" syntax.
        // https://secure.php.net/manual/en/migration70.new-features.php#migration70.new-features.unicode-codepoint-escape-syntax
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $params[] = ['foo', "\u{1D107}foo"];
        }

        return $params;
    }

    /**
     * @dataProvider getResponses
     *
     * @param string $expected
     * @param string $actual
     */
    public function testRepair($expected, $actual)
    {
        $repair = new ResponseRepair();
        $this->assertEquals($expected, $repair->repair($actual));
    }
}
