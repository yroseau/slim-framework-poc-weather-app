<?php

namespace WeatherApp\Controllers\Front;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use WeatherApp\Controllers\ControllerInterface;
use WeatherApp\Helpers\WeatherSettingsHelper;

/** @noinspection PhpUnused -> Class auto-loaded from routing.yml */
class HomeController implements ControllerInterface
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {

        $infoCards = [];

        // example: {"cards": [{"city": "Montpellier", "countryCode": "fr"}, {"city": "Paris", "countryCode": "fr"}, {"city": "Vancouver"}, {"city": "Los Angeles"}]}
        $cookies = $request->getCookieParams();

        if (array_key_exists('settings', $cookies)) {
            try {
                $settings = json_decode($cookies['settings']);

                if (property_exists($settings, 'cards')) {

                    $globalOptions = null;
                    if (property_exists($settings, 'options')) {
                        $globalOptions = $settings->options;
                    }

                    /** @var WeatherApiInterface $api */
                    $api = $this->container->get('weather_api');

                    foreach ($settings->cards as $cardOptions) {

                        if (array_key_exists('city', $cardOptions)) {

                            $weatherInfo = $api->getWeatherInfo(
                                $cardOptions->city,
                                $cardOptions->countryCode ?? null,
                                $cardOptions->units ?? $globalOptions->units ?? null
                            );
                            if ($weatherInfo !== null) {
                                array_push(
                                    $infoCards,
                                    $weatherInfo
                                // $this->generateCityCardData($weatherInfo)
                                );
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // @todo return an error
            }
        }

        $params = [
            'cards' => $infoCards,
        ];

        return $this->container->get('view')->render(
            $response,
            'pages/home.html.twig',
            $params
        );

    }

//    /**
//     * Generate data from WeatherInfoInterface to template
//     *
//     * @param null|WeatherInfoInterface $weatherInfo
//     *
//     * @return array
//     */
//    private function generateCityCardData(?WeatherInfoInterface $weatherInfo): array
//    {
//        if ($weatherInfo === null) {
//            return [];
//        }
//
//        return [
//            'city'    => $weatherInfo->getCity()->getName(),
//            'country' => $weatherInfo->getCountry()->getName(),
//            'weather' => [
//                'icon'      => $weatherInfo->getWeatherIcon(),
//                'temp'      => $weatherInfo->getTemperature(),
//                'temp_unit' => $weatherInfo->getTemperatureUnit(),
//            ],
//        ];
//    }
}
