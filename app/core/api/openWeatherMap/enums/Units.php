<?php

namespace WeatherApp\Core\API\OpenWeatherMap\Enums;

// @todo use SplEnum
// @see https://www.php.net/manual/fr/class.splenum.php


abstract class Units // extends SplEnum
{
    public const __default = self::STANDARD;

    public const STANDARD = null;
    public const METRIC   = 'metric';
    public const IMPERIAL = 'imperial';
}
