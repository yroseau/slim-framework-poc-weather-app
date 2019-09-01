<?php

namespace WeatherApp\Core\API\Interfaces;

use WeatherApp\Core\API\Interfaces\Data\WeatherInfoInterface;
use Psr\Container\ContainerInterface;

interface WeatherApiInterface
{

    public function __construct(?ContainerInterface $container);

    public function getWeatherInfo(
        string $cityName,
        ?string $countryCode = null,
        ?string $units = null
    ): ?WeatherInfoInterface;

}