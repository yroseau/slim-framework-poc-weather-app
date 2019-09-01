<?php

namespace WeatherApp\Core\API\Interfaces\Data;

interface WeatherInfoInterface
{

    public function getCity(): CityInterface;

    public function getCountry(): CountryInterface;

    public function getWeatherIcon(): string;

    public function getTemperature(): float;

    public function getTemperatureUnit(): string;

    public function getWeatherTitle(): string;

    public function getWeatherDescription(): string;

}
