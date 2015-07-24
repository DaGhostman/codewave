<?php
namespace Wave\Framework\Router;

use \FastRoute\RouteCollector;
use \FastRoute\DataGenerator;
use \FastRoute\RouteParser;

class ExtendedRouteCollector extends RouteCollector
{
    /**
     * @type mixed
     */
    protected $cacheProvider;
    protected $cacheLifetime = 0;

    /**
     * Constructs a route collector.
     * Additionally, the constructor accepts a third optional parameter, which is  an array
     * of 1 mandatory and 1 optional keys. 'provider' is mandatory as it will be used as cache provider and
     * the second one is 'ttl', which is to determine the lifetime of the cache entry.
     *
     * Note that the provider entry must have methods 'fetch', 'save', 'contains' to ensure compatibility
     *
     * @param RouteParser   $routeParser
     * @param DataGenerator $dataGenerator
     * @param array         $cache assoc array with entries 'provider' for the caching provider and optionally
     *                             a second one (Optional) 'ttl' for the cache lifetime.
     */
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator, array $cache = null)
    {
        parent::__construct($routeParser, $dataGenerator);
        if (is_array($cache) && array_key_exists('provider', $cache)) {
            if (!method_exists($this->cacheProvider, 'fetch') ||
                !method_exists($this->cacheProvider, 'contains') ||
                !method_exists($this->cacheProvider, 'save')
            ) {
                throw new \InvalidArgumentException(
                    'Cache provider must implement methods "fetch", "contains", "save"'
                );
            }

            $this->cacheProvider = $cache['provider'];
            if (array_key_exists('ttl', $cache)) {
                $this->cacheLifetime = $cache['ttl'];
            }
        }
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData()
    {
        if ($this->cacheProvider !== null &&
            $this->cacheProvider->contains('routerCache')) {
            return $this->cacheProvider->fetch('routerCache');
        }

        return parent::getData();
    }

    /**
     * Saves all data to cache right before destroying the object
     */
    public function __destruct()
    {
        if ($this->cacheProvider !== null && !$this->cacheProvider->contains('routerCache')) {
            $this->cacheProvider->save(
                'routerCache',
                $this->getData(),
                (int) $this->cacheLifetime
            );
        }
    }
}
