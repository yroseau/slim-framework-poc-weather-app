<?php

namespace WeatherApp;

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__.'/public/'.$url['path'];
    if (is_file($file)) {
        return false;
    }
}

// Autoload of WeatherApp
spl_autoload_register(function ($name) {
    $namespace = explode('\\', $name);
    if ($namespace[0] !== __NAMESPACE__) {
        return;
    }
    $namespace = array_splice($namespace, 1);
    if (count($namespace) === 1) {
        return;
    }
    $path = __DIR__;
    foreach($namespace as $i => $e) {
        if ($i !== count($namespace) - 1) {
            $path .= '/' . strtolower($e);
        } else {
            $path .= '/' . $e . '.php';
        }
    }
    // @todo use a logger
    // syslog(LOG_DEBUG, "[Autoload] $path - Loading...");
    if (!file_exists($path)) {
        error_log("[Autoload] $path - File not found -> Impossible load file for \"$name\"");
        return;
    }
    /** @noinspection PhpIncludeInspection */
    require_once($path); // or die("[500] Impossible to start application");
    // syslog(LOG_DEBUG, "[Autoload] $path - File loaded with success");
});

use DI\Container;
use Slim\Factory\AppFactory;
use WeatherApp\Core\API\OpenWeatherMap\OpenWeatherMapApi;
use WeatherApp\Core\Routing\RoutingLoader;

require __DIR__.'/vendor/autoload.php';

define('ENV', 'DEV');

// Instantiate App
$container = new Container();
$app = AppFactory::create(null, $container);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Load routing
$routing = new RoutingLoader($app);
$routing->loadFromFile('config/routing.yml');

// Register weather app
$container->set(
    'weather_api',
    function ($container) {
        return new OpenWeatherMapApi($container);
    }
);

// Register memcached
$container->set(
    'memcached',
    function ($container) {
        $memcached = new \Memcached();
        $memcached->addServer('memcached', 11211);

        return $memcached;
    }
);

// Register view
$container->set(
    'view',
    function ($container) {
        $twigSettings = [];
        switch (ENV) {
            case 'DEV':
                $twigSettings['debug'] = ENV;
                break;
            default:
                $twigSettings['cache'] = __DIR__.'/resources/templates/.cache';
        }
        $view = new \Slim\Views\Twig(
            __DIR__.'/resources/templates', $twigSettings
        );

        if (ENV === 'DEV') {
            $view->addExtension(new \Twig\Extension\DebugExtension());
        }

        // Instantiate and add Slim specific extension
//        $router = $container->get('router');
//        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
//        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

        return $view;
    }
);

// Run app
$app->run();