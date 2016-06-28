<?php
/**
 * AnimeDb package
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Tests\Service\Client\Cache;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\ExpireResolver;

class ExpireResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpireResolver
     */
    protected $resolver;

    /**
     * @var array
     */
    protected $requests = [
        'anime' => '+1 week',
        'categorylist' => '+6 month',
        'randomrecommendation' => null,
        'randomsimilar' => null,
        'mylistsummary' => null,
        'hotanime' => '+1 day',
        'main' => '+1 day',
    ];

    protected function setUp()
    {
        $this->resolver = new ExpireResolver($this->requests);
    }

    /**
     * @return array
     */
    public function getRequests()
    {
        $params = [
            ['not_exists_request'],
        ];
        foreach ($this->requests as $request => $modify) {
            $params[] = [$request];
        }

        return $params;
    }

    /**
     * @dataProvider getRequests
     *
     * @param string $request
     */
    public function testGetExpire($request)
    {
        $date = new \DateTime('28-06-2016 16:30:00');
        $expected = clone $date;

        if (!isset($this->requests[$request])) {
            $expected->modify(ExpireResolver::DEFAULT_MODIFY);
        } elseif ($this->requests[$request]) {
            $expected->modify($this->requests[$request]);
        } else {
            $expected = null;
        }

        $this->assertEquals($expected, $this->resolver->getExpire($request, $date));
    }
}
