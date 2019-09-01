<?php

namespace WeatherApp\Controllers;

use Psr\Container\ContainerInterface;

interface ControllerInterface
{

    public function __construct(ContainerInterface $container = null);

}