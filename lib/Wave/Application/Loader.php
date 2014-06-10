<?php

namespace Wave\Application;

use Wave\Http\Factory;

class Loader
{
    
    public $environement = null;
    public $http = null;
    public $container = null;
    
    protected $controller = null;
    
    protected $defaultConfig = null;
    
    protected $config = null;
    
    public function __construct($config = array())
    {
        $this->defaultConfig = array(
            'mode' => 'devel',
            'debug' => true,
            'handlers' => array(
                'request' => '\Wave\Http\Request',
                'response' => '\Wave\Http\Response',
                'environment' => '\Wave\Application\Environment',
                'http' => '\Wave\Http\Factory',
                'controller' => '\Wave\Application\Controller'
            ),
            'environment' => array(
                'request.protocol' => (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'),
                'request.port' => (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80),
                'request.uri' => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/'),
                'request.method' => (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET')
            ),
            'routes.case_sensitive' => false
        );
        
        
        
            
        $this->config = array_merge($this->defaultConfig, $config);
        $env_handler = $this->config['handlers']['environment'];
        $ctrl_handler = $this->config['handlers']['controller'];
        
        $this->controller = new $ctrl_handler;
        $this->environement = new $env_handler($this->config['environment']);
        
    }
    
    public function bootstrap()
    {
        $this->controller->state('http_before')
            ->notify($this->environement);
        
        $this->http = new $this->config['handlers']['http'](
            $this->config['handlers']['request'],
            $this->config['handlers']['response'],
            $this->environement
        );
        
        $this->controller->state('http_after')
            ->notify($this->environement);
    }
    
    public function get()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('GET', 'HEAD');
    }
    
    public function post()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('POST');
    }
    
    public function put()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('PUT');
    }
    
    public function delete()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('DELETE');
    }
    
    public function trace()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('TRACE');
    }
    
    public function connect()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('CONNECT');
    }
    
    public function options()
    {
        $args = func_get_args();
        
        return $this->mapRoute($args)->via('OPTIONS');
    }
    
    protected function mapRoute($args)
    {
        $pattern = array_shift($args);
        $callable = array_pop($args);
        $route = new \Wave\Route($pattern, $callable, $this->config['routes.case_sensitive']);
        $this->controller->map($route);
    
        return $route;
    }
    
    public function run()
    {
        $this->controller->state('mapping_before')
            ->notify($this->environement);
        
        try {
            $dispatched = false;
            $matchedRoutes = $this->controller->getMatchedRoutes(
                $this->environement['request.method'],
                $this->environement['request.uri']
            );
            
            foreach ($matchedRoutes as $route) {
                try {
                    $this->controller->state('dispatch_before')
                        ->notify();
                    
                    $dispatched = $route->dispatch();
                    
                    $this->controller->state('dispatch_after')
                        ->notify();
                    
                    if ($dispatched) {
                        
                        break;
                    }
                } catch (\Wave\Application\State\Pass $e) {
                    continue;
                } catch (\Wave\Application\State\Halt $e) {
                    break;
                }
            }
            if (!$dispatched) {
                $this->http->response()
                    ->notFound()
                    ->send();
            }
        } catch (\Exception $e) {
            if ($this->config['debug']) {
                throw $e;
            }
        }
        
        $this->controller->state('mapping_after')
            ->notify($this->environement);
        
        $this->controller->state('application_after')
            ->notify($this->environement);
    }
}
