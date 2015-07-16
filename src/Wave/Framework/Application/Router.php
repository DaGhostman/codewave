<?php
namespace Wave\Framework\Application;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Wave\Framework\Exceptions\HttpNotAllowedException;
use Wave\Framework\Exceptions\HttpNotFoundException;
use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

class Router
{
    /**
     * @var \FastRoute\RouteCollector
     */
    protected $collector;
    /**
     * @var array
     */
    protected $namedRoutes = [];
    private $prefix;

    public function __construct(array $options = [])
    {
        foreach ($options as $option => $value) {
            $this->$option = $value;
        }

        if ($this->prefix !== null) {
            $this->setPrefix($this->prefix);
        }

        $this->collector = new RouteCollector(
            new Std(),
            new GroupCountBased()
        );
    }

    private function setPrefix($prefix)
    {
        if ($prefix !== null && substr($prefix, 0, 1) !== '/') {
            throw new \LogicException('Prefix must begin with "/"');
        }

        $this->prefix = $prefix;
    }

    public function get($pattern, array $callback, $name = null)
    {
        $this->addRoute('get', $pattern, $callback, $name);

        return $this;
    }

    private function addRoute($method, $pattern, array $callback, $name = null)
    {
        $this->collector->addRoute(strtoupper($method), (string)$this->prefix . $pattern, $callback);

        if ($name !== null) {
            $this->namedRoutes[$name] = $this->prefix . $pattern;
        }
    }

    public function post($pattern, array $callback, $name = null)
    {
        $this->addRoute('post', $pattern, $callback, $name);

        return $this;
    }

    public function put($pattern, array $callback, $name = null)
    {
        $this->addRoute('put', $pattern, $callback, $name);

        return $this;
    }

    public function patch($pattern, array $callback, $name = null)
    {
        $this->addRoute('patch', $pattern, $callback, $name);

        return $this;
    }

    public function delete($pattern, array $callback, $name = null)
    {
        $this->addRoute('delete', $pattern, $callback, $name);

        return $this;
    }

    public function options($pattern, array $callback, $name = null)
    {
        $this->addRoute('options', $pattern, $callback, $name);

        return $this;
    }

    public function group($callback, array $options = [])
    {
        $oldPrefix = $this->prefix;
        if (array_key_exists('prefix', $options)) {
            $this->setPrefix($options['prefix']);
        }

        call_user_func($callback, $this);
        $this->setPrefix($oldPrefix);

        return $this;
    }

    public function route($name, array $args = [])
    {
        if (!array_key_exists($name, $this->namedRoutes)) {
            throw new \InvalidArgumentException(
                sprintf('Route with name "%s" does not exist', $name)
            );
        }

        if (!is_array($this->namedRoutes[$name])) {
            $pattern = $this->namedRoutes[$name];
            $patterns = [];
            preg_match_all('#\{([A-Z\}:\[\]|+_-])+#i', $pattern, $matches);

            foreach ($matches[0] as $match) {
                if (strpos($match, ':') !== false) {
                    list($param, $validator) = explode(':', substr($match, 1, -1));
                    $patterns[$param] = $validator;

                    $pattern = str_replace($match, '{' . $param .'}', $pattern);
                    continue;
                }
                $patterns[substr($match, 1, -1)] = null;
            }
            $this->namedRoutes[$name] = [$pattern, $patterns];
        }

        $replace = [];

        if (array_diff_key($args, $this->namedRoutes[$name][1]) !== []) {
            throw new \LogicException(
                'Number of arguments does not match the number of bound parameters'
            );
        }

        foreach ($args as $key => $value) {
            if (preg_match('/' . $this->namedRoutes[$name][1][$key] . '/', $value) !== 1) {
                throw new \InvalidArgumentException(sprintf(
                    'The value for parameter "%s" does not match the predefined pattern',
                    $key
                ));
            }
            $replace['{' . $key . '}'] = $value;
        }

        return strtr($this->namedRoutes[$name][0], $replace);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws HttpNotAllowedException
     * @throws HttpNotFoundException
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response)
    {
        $d = new \FastRoute\Dispatcher\GroupCountBased($this->collector->getData());
        $r = $d->dispatch(
            ($request->getMethod() === 'HEAD' ? 'GET' : $request->getMethod()),
            $request->getUrl()
                ->getPath()
        );

        switch ($r[0]) {
            case 0:
                throw new HttpNotFoundException('Route not found');
                break;
            case 1:
                call_user_func($r[1], $request, $r[2], $response);
                break;
            case 2:
                throw new HttpNotAllowedException('Method not allowed', 0, null, $r[1]);
                break;
        }
    }
}
