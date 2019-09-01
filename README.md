# Weather App

>  This project is used to POC Slim framework PHP.
>
> CAUTION: this is not a project make in state of the art !

## Requirements

* Docker 17+

## Start app

```bash
git clone git@github.com:yroseau/slim-framework-poc-weather-app.git
cd slim-framework-poc-weather-app/docker
cp .env.dist .env
# Update .env file
docker-compose up
```

## Use web app

Go to http://localhost:8000/

## Use api

### Example
* Return temperature in kelvin unit \
http://localhost:8000/api/weather/fr/paris \
http://localhost:8000/api/weather/fr/paris/standard

* Return temperature in celsius unit \
http://localhost:8000/api/weather/fr/paris/metric

* Return temperature in fahrenheit unit \
http://localhost:8000/api/weather/fr/paris/imperial

## TODO

* Use Webpack + babel + uglify
* Translation
* Scss
* Logger
* Use nginx

