<?php

namespace WeatherApp\Controllers\Api;

use WeatherApp\Core\API\Interfaces\WeatherApiInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use WeatherApp\Controllers\ControllerInterface;

/** @noinspection PhpUnused -> Class auto-loaded from routing.yml */
class ApiController implements ControllerInterface
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCityWeatherAction(Request $request, Response $response, $args): Response
    {
        /** @var WeatherApiInterface $api */
        $api = $this->container->get('weather_api');
        $units = array_key_exists('units', $args) ? $args['units'] : null;
        $info = $api->getWeatherInfo($args['city'], $args['countryCode'], $units);

        $response->getBody()->write(json_encode([
            'city' => [
                'name' => $info->getCity()->getName(),
            ],
            'weather' => [
                'icon' => $info->getWeatherIcon(),
                'title' => $info->getWeatherTitle(),
                'description' => $info->getWeatherDescription(),
                'temp' => [
                    'value' => $info->getTemperature(),
                    'unit' => $info->getTemperatureUnit(),
                ],
            ],
        ]));

        return $response->withHeader('Content-type', 'application/json');
    }
}