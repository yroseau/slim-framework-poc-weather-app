<?php

namespace WeatherApp\Core\API\OpenWeatherMap\Data;

use WeatherApp\Core\API\Interfaces\Data\CountryInterface;

class Country implements CountryInterface
{
    private $name;

    /**
     * @return mixed
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

}