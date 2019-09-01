<?php

namespace WeatherApp\Controllers\Front;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use WeatherApp\Controllers\ControllerInterface;
use WeatherApp\Helpers\WeatherSettingsHelper;

/** @noinspection PhpUnused -> Class auto-loaded from routing.yml */
class SettingsController implements ControllerInterface
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $cookies = $request->getCookieParams();

        $settings = [];
        if (array_key_exists('settings', $cookies)) {
            try {
                $settings = json_decode($cookies['settings']);
            } catch (\Exception $e) {
                // @todo return an error ??
            }
        }

        $params = [
            'settings' => $settings,
        ];

        return $this->container->get('view')->render(
            $response,
            'pages/settings.html.twig',
            $params
        );

    }
}