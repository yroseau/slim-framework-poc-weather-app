<?php

declare(strict_types=1);

namespace WeatherApp\Core\Routing;

class Route
{
    private $routeName;
    private $httpMethods;
    private $path;

    private $controllerName;
    private $actionName;

    /**
     * Route constructor.
     *
     * @param string   $routeName
     * @param string   $path
     * @param string[] $httpMethods
     */
    public function __construct(string $routeName, string $path, array $httpMethods = ['GET'])
    {
        $this->routeName = $routeName;
        $this->path = $path;
        $this->setHttpMethods($httpMethods);
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string[]
     */
    public function getHttpMethods(): array
    {
        return $this->httpMethods;
    }

    /**
     * @param array $httpMethods
     */
    public function setHttpMethods(array $httpMethods)
    {
        // @todo check if they are valid methods.
        // @todo use an enum instead string. Check if already defined in framework.
        $this->httpMethods = array_map('strtoupper', $httpMethods);
    }

    /**
     * Generate and return callable action or controller
     *
     * @return string
     * @throws \LogicException
     * @todo use custom exception
     */
    public function getCallable(): string
    {
        // prepare invokable
        $callable = $this->getControllerName();
        if ($callable === null) {
            throw new \LogicException("Impossible to get callable action before set controller name");
        }

        // add action
        $actionName = $this->getActionName();
        if ($actionName !== null) {
            $callable .= ":".$actionName;
        }

        // return invokable
        return $callable;
    }

    /**
     * @return string|null
     */
    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    /**
     * @param mixed $controllerName
     */
    public function setControllerName(string $controllerName)
    {
//        $controllerName = rtrim($controllerName, '.php');
//        $controllerName = end(explode('/', $controllerName));
        $this->controllerName = $controllerName;
    }

    /**
     * @return string|null
     */
    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    /**
     * @return string
     */
    public function getResolvedActionName(): string
    {
        return $this->getActionName() ?? '__invoke';
    }

    /**
     * @param string $actionName
     */
    public function setActionName(string $actionName)
    {
        $actionName = ltrim($actionName, ':');
        $this->actionName = $actionName;
    }

}