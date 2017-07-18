<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\Guzzle;

use GuzzleHttp\RequestOptions;

class RequestConfigurator
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $app_code
     *
     * @return RequestConfigurator
     */
    public function setAppCode($app_code)
    {
        $this->options[RequestOptions::HEADERS]['User-Agent'] = $app_code;

        return $this;
    }

    /**
     * @param string $app_client
     *
     * @return RequestConfigurator
     */
    public function setAppClient($app_client)
    {
        $this->options[RequestOptions::QUERY]['client'] = $app_client;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return RequestConfigurator
     */
    public function setTimeout($timeout)
    {
        $this->options[RequestOptions::TIMEOUT] = $timeout;

        return $this;
    }

    /**
     * @param string $proxy
     *
     * @return RequestConfigurator
     */
    public function setProxy($proxy)
    {
        $this->options[RequestOptions::PROXY] = $proxy;

        return $this;
    }

    /**
     * @param string $app_version
     *
     * @return RequestConfigurator
     */
    public function setAppVersion($app_version)
    {
        $this->options[RequestOptions::QUERY]['clientver'] = $app_version;

        return $this;
    }

    /**
     * @param string $protocol_version
     *
     * @return RequestConfigurator
     */
    public function setProtocolVersion($protocol_version)
    {
        $this->options[RequestOptions::QUERY]['protover'] = $protocol_version;

        return $this;
    }

    /**
     * @param string $request
     * @param array  $query
     *
     * @return RequestConfigurator
     */
    public function withRequest($request, array $query = [])
    {
        $new = clone $this;
        $new->options['query'] = isset($new->options['query']) ? $new->options['query'] : [];
        $new->options['query'] = $new->options['query'] + ['request' => $request] + $query;

        return $new;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
