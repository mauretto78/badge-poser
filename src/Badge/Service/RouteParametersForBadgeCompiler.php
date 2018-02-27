<?php

namespace App\Badge\Service;

use Symfony\Component\Routing\Router;

class RouteParametersForBadgeCompiler
{
    private $routes;
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->routes = $this->router->getRouteCollection();
    }

    public function compile($badge)
    {
        $parameters = array();
        $route = $this->routes->get($badge['route']);

        $routeParameters = array_keys(array_merge($route->getDefaults(), $route->getRequirements()));

        foreach ($routeParameters as $routeParameter) {
            if (array_key_exists($routeParameter, $badge)) {
                $parameters[$routeParameter] = $badge[$routeParameter];
            }
        }

        return $parameters;
    }
}