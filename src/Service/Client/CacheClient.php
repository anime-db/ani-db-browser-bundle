<?php
/**
 * AnimeDb package
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\ExpireResolver;
use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Cache\Storage\StorageInterface;

class CacheClient implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var ExpireResolver
     */
    protected $resolver;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param ClientInterface $client
     * @param ExpireResolver $resolver
     * @param StorageInterface $storage
     */
    public function __construct(ClientInterface $client, ExpireResolver $resolver, StorageInterface $storage)
    {
        $this->client = $client;
        $this->resolver = $resolver;
        $this->storage = $storage;
    }

    /**
     * @param int $timeout
     *
     * @return CacheClient
     */
    public function setTimeout($timeout)
    {
        $this->client->setTimeout($timeout);

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return CacheClient
     */
    public function setProxy($proxy)
    {
        $this->client->setProxy($proxy);

        return $this;
    }

    /**
     * @param string $request
     * @param array $params
     *
     * @return string
     */
    public function get($request, array $params = [])
    {
        $expires = $this->resolver->getExpire($request, new \DateTime());
        if (!$expires) {
            return $this->client->get($request, $params);
        }

        $key = http_build_query(['request' => $request] + $params);
        if (!($response = $this->storage->get($key))) {
            $response = $this->client->get($request, $params);
            $this->storage->set($key, $response, $expires);
        }

        return $response;
    }
}
