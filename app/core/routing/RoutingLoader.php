<?php

declare(strict_types=1);

namespace WeatherApp\Core\Routing;

use LogicException;
use Psr\Container\ContainerInterface;
use Slim\App;

require __DIR__.'/Route.php';

class RoutingLoader
{

    /** @var App */
    protected $app;

    /**
     * RoutingLoader constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Load routing from file
     *
     * @param string $filePath
     */
    public function loadFromFile(string $filePath): void
    {
        $filePath = realpath($filePath);

        if (!file_exists($filePath)) {
            throw new LogicException('['.__CLASS__.'] File not found: '.$filePath);
        }

        // get extension
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        // choose loader in term of extension file
        switch ($ext) {
            case 'yml':
            case 'yaml':
                $this->loadFromYamlFile($filePath);
                break;
            default:
                throw new LogicException('['.__CLASS__.'] Unsupported file extension: '.$ext);
        }
    }

    /**
     * Load routing for Yaml file
     *
     * @param string $filePath
     */
    protected function loadFromYamlFile(string $filePath): void
    {
        $data = yaml_parse_file($filePath);

        if (!array_key_exists('routes', $data)) {
            error_log('['.__CLASS__.'] Routes not found in: '.$filePath);

            return;
        }

        $routes = $data['routes'];

        foreach ($routes as $routeName => $routeSettings) {
            // test required settings
            if (!array_key_exists('path', $routeSettings)) {
                error_log('['.__CLASS__.'] Route "'.$routeName.'" don\'t have path');
                continue;
            }
            if (!array_key_exists('controller', $routeSettings)) {
                error_log('['.__CLASS__.'] Route "'.$routeName.'" don\'t have controller');
                continue;
            }

            try {
                // check if the controller file exists
                $controllerName = $routeSettings['controller'];
                $this->assertController($controllerName);

                // create route
                $route = new Route($routeName, $routeSettings['path']);
                $route->setControllerName($controllerName);

                // add optional field: method (default: get)
                if (array_key_exists('method', $routeSettings)) {
                    $httpMethods = $routeSettings['method'];
                    if (is_string($httpMethods)) {
                        $httpMethods = [$httpMethods];
                    }
                    $route->setHttpMethods($httpMethods);
                }

                // add optional field: method (default: __invoke)
                if (array_key_exists('action', $routeSettings)) {
                    $route->setActionName($routeSettings['action']);
                }

                // check if action exists for this controller
                $this->assertMethod($controllerName, $route->getResolvedActionName());

                // add the route
                $this->addRoute($route);
            } catch (\Exception $e) {
                error_log('['.__CLASS__.'] Impossible to register route: '.$e->getMessage());
            }
        }
    }

    /**
     * Check controller exists and implements ControllerInterface
     *
     * @param string $controllerName
     */
    protected function assertController(string $controllerName): void
    {
        if (!class_exists($controllerName)) {
            throw new LogicException('Controller not found: '.$controllerName);
        }

        if (!in_array('WeatherApp\Controllers\ControllerInterface', class_implements($controllerName))) {
            throw new LogicException('Controller does not implements WeatherApp\Controllers\ControllerInterface: '.$controllerName);
        }
    }

    protected function assertMethod(string $controllerName, string $methodName)
    {
        if (!method_exists($controllerName, $methodName)) {
            throw new LogicException("Action \"$methodName\" not found in controller \"$controllerName\"");
        }
    }

    /**
     * Add route to slim app
     *
     * @param Route $route
     */
    protected function addRoute(Route $route): void
    {
        $this->app
            ->map(
                $route->getHttpMethods(),
                $route->getPath(),
                $route->getCallable()
            )
            ->setName($route->getRouteName());

        $controllerName = $route->getControllerName();
        $this->app->getContainer()->set(
            $controllerName,
            function (ContainerInterface $c) use ($controllerName) {
                return new $controllerName($c);
            }
        );

        // syslog(LOG_INFO, 'Add route '.$route->getRouteName().': '.$route->getPath().' -> '.$route->getCallable());
    }

    function get(&$var, $default = null)
    {
        return isset($var) ? $var : $default;
    }

}