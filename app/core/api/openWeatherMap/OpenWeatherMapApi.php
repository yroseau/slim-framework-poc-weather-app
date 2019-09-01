<?php

namespace WeatherApp\Core\API\OpenWeatherMap;

use stdClass;
use WeatherApp\Core\API\Interfaces\Data\WeatherInfoInterface;
use WeatherApp\Core\API\Interfaces\WeatherApiInterface;
use WeatherApp\Core\API\OpenWeatherMap\Data\WeatherInfo;
use WeatherApp\Core\API\OpenWeatherMap\Enums\Units;
use Psr\Container\ContainerInterface;

/**
 * Class WeatherApi
 */
class OpenWeatherMapApi implements WeatherApiInterface
{

    private const OPENWEATHERMAP_API_URL = "http://api.openweathermap.org/data/2.5/weather";

    /** @var \Memcached|null */
    private $memcached     = null;
    private $cacheDuration = 500;

    public function __construct(?ContainerInterface $container)
    {
        if (
            $container !== null &&
            $container->has('memcached')
        ) {
            /** @noinspection PhpUnhandledExceptionInspection -> We have checked above with "has" function */
            $this->memcached = $container->get('memcached');
        }
    }

    private function getPrivateApiKey(): string {
        return array_key_exists('OPENWEATHERAPI_PRIVATE_API_KEY', $_ENV) ? $_ENV['OPENWEATHERAPI_PRIVATE_API_KEY'] : '';
    }

    /**
     * Return weather info by city
     *
     * @see https://openweathermap.org/current#name
     *
     * @param string      $cityName
     * @param string|null $countryCode
     * @param string|null $units [metric, imperial, kelvin]
     *
     * @return WeatherInfoInterface
     */
    public function getWeatherInfo(
        string $cityName,
        ?string $countryCode = null,
        ?string $units = null
    ): ?WeatherInfoInterface {

        $units = $this->parseUnits($units);

        $json = $this->callApi(
            $cityName.($countryCode ? ",$countryCode" : '').($units ? '&units='.$units : '')
        );

        if ($json === false) {
            return null;
        }

        /*

        Example of openWeatherMap app response

        {
            "coord": {"lon":-0.13,"lat":51.51},
            "weather":[{"id":300,"main":"Drizzle","description":"light intensity drizzle","icon":"09d"}],
            "base":"stations",
            "main":{"temp":280.32,"pressure":1012,"humidity":81,"temp_min":279.15,"temp_max":281.15},
            "visibility":10000,
            "wind":{"speed":4.1,"deg":80},
            "clouds":{"all":90},
            "dt":1485789600,
            "sys":{"type":1,"id":5091,"message":0.0103,"country":"GB","sunrise":1485762037,"sunset":1485794875},
            "id":2643743,
            "name":"London",
            "cod":200
        }

         */

        // @todo add temp, temp min, temp max, and wind (speed and deg)
        $info = new WeatherInfo();
        $info->setCode($json->weather[0]->id);
        $info->setWeatherTitle($json->weather[0]->main);
        $info->setWeatherDescription($json->weather[0]->description);
        $info->setCityName($json->name);
        $info->setTemperature($json->main->temp);
        $info->setUnits($units);

        return $info;
    }

    /**
     * Parse units
     * Return null if units is kelvin, because it is a standard format
     *
     * @see https://openweathermap.org/current#data
     *
     * @param string|null $units
     *
     * @return null|string
     */
    private function parseUnits(string $units = null): ?string
    {
        switch ($units) {
            case Units::METRIC:
            case Units::IMPERIAL:
            case Units::STANDARD:
                return $units;
            case 'kelvin':
            default:
                return Units::STANDARD;
        }
    }

    /**
     * @see  https://openweathermap.org/price
     *
     * @param $query
     *
     * @return stdClass
     */
    protected function callApi($query): stdClass
    {
        $cacheKey = null;

        if ($this->memcached !== null) {
            $hash = hash("md5", $query);
            $cacheKey = "openweathermap_call_$hash";
            $data = $this->memcached->get($cacheKey);
            if ($data !== false) {
                // syslog(LOG_DEBUG, "[MEMCACHED] Cache found for openweathermap query: $query");
                return json_decode($data);
            }
        }

        $data = file_get_contents(
            self::OPENWEATHERMAP_API_URL . "?q=$query&APPID=" . $this->getPrivateApiKey()
        );

        if ($this->memcached !== null) {
            // @todo cached on success only
            // @todo warning if $this->cacheDuration > 60*60*24*30
            $this->memcached->set(
                $cacheKey,
                $data,
                $this->cacheDuration
            );
        }

        return json_decode($data);
    }
}