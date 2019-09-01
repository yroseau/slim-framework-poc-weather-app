<?php

namespace WeatherApp\Core\API\OpenWeatherMap\Data;

use WeatherApp\Core\API\Common\TemperatureUnit;
use WeatherApp\Core\API\Interfaces\Data\CityInterface;
use WeatherApp\Core\API\Interfaces\Data\CountryInterface;
use WeatherApp\Core\API\Interfaces\Data\WeatherInfoInterface;
use WeatherApp\Core\API\OpenWeatherMap\Enums\Units;

class WeatherInfo implements WeatherInfoInterface
{
    /** @var int */
    private $code;

    /** @var float */
    private $temperature;

    /** @var string */
    private $units;

    /** @var City */
    private $city;

    /** @var Country */
    private $country;

    /** @var string */
    private $weatherTitle;

    /** @var string */
    private $weatherDescription;

    public function __construct()
    {
        $this->city = new City();
        $this->country = new Country();
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * @param float $temperature
     */
    public function setTemperature(float $temperature): void
    {
        $this->temperature = $temperature;
    }

    /**
     * @param mixed $cityName
     */
    public function setCityName(?string $cityName): void
    {
        $this->city->setName($cityName);
    }

    public function getCity(): CityInterface
    {
        return $this->city;
    }

    public function getCountry(): CountryInterface
    {
        return $this->country;
    }

    public function getTemperatureUnit(): string
    {
        switch ($this->units) {
            case Units::METRIC:
                return TemperatureUnit::CELSIUS;
            case Units::IMPERIAL:
                return TemperatureUnit::FAHRENHEIT;
            default:
                return TemperatureUnit::KELVIN;
        }
    }

    /**
     * @param string $units
     */
    public function setUnits(?string $units): void
    {
        $this->units = $units;
    }

    /**
     * @return string
     */
    public function getWeatherIcon(): string
    {
        switch ($this->code) {
            case 200:
            case 201:
            case 202:
            case 230:
            case 231:
            case 232:
                $weatherIcon = "wi-thunderstorm";
                break;
            case 210:
            case 211:
            case 212:
            case 221:
                $weatherIcon = "wi-lightning";
                break;
            case 300:
            case 301:
            case 321:
            case 500:
                $weatherIcon = "wi-sprinkle";
                break;
            case 302:
            case 311:
            case 312:
            case 314:
            case 501:
            case 502:
            case 503:
            case 504:
                $weatherIcon = "wi-rain";
                break;
            case 310:
            case 511:
            case 611:
            case 612:
            case 615:
            case 616:
            case 620:
                $weatherIcon = "wi-rain-mix";
                break;
            case 313:
            case 520:
            case 521:
            case 522:
            case 701:
                $weatherIcon = "wi-showers";
                break;
            case 531:
            case 901:
                $weatherIcon = "wi-storm-showers";
                break;
            case 600:
            case 601:
            case 621:
            case 622:
                $weatherIcon = "wi-snow";
                break;
            case 602:
                $weatherIcon = "wi-sleet";
                break;
            case 711:
                $weatherIcon = "wi-smoke";
                break;
            case 721:
                $weatherIcon = "wi-day-haze";
                break;
            case 761:
            case 762:
            case 731:
                $weatherIcon = "wi-dust";
                break;
            case 741:
                $weatherIcon = "wi-fog";
                break;
            case 771:
            case 801:
            case 802:
            case 803:
                $weatherIcon = "wi-cloudy-gusts";
                break;
            case 781:
            case 900:
                $weatherIcon = "wi-tornado";
                break;
            case 800:
                $weatherIcon = "wi-day-sunny";
                break;
            case 804:
                $weatherIcon = "wi-cloudy";
                break;
            case 902:
                $weatherIcon = "wi-hurricane";
                break;
            case 903:
                $weatherIcon = "wi-snowflake-cold";
                break;
            case 904:
                $weatherIcon = "wi-hot";
                break;
            case 905:
                $weatherIcon = "wi-windy";
                break;
            case 906:
                $weatherIcon = "wi-hail";
                break;
            case 957:
                $weatherIcon = "wi-strong-wind";
                break;
            default:
                $weatherIcon = 'wi-na';
        }

        return $weatherIcon;
    }

    public function setWeatherTitle($title)
    {
        $this->weatherTitle = $title;
    }

    public function setWeatherDescription($description)
    {
        $this->weatherDescription = $description;
    }

    /**
     * @return string
     */
    public function getWeatherTitle(): string
    {
        return $this->weatherTitle;
    }

    /**
     * @return string
     */
    public function getWeatherDescription(): string
    {
        return $this->weatherDescription;
    }

}