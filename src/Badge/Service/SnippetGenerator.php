<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Simone Di Maulo <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Service;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class SnippetGenerator
 *
 * @author Simone Di Maulo <toretto460@gmail.com>
 * @author Matteo Adamo <adamo.matteo@gmail.com>
 * @author Francesco Face <francesco.face@gmail.com>
 */
class SnippetGenerator
{
    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var array $badges
     */
    private $badges;

    /**
     * @var array $allInBadges
     */
    private $allInBadges;

    /**
     * @var null|RouteCollection
     */
    private $routes;

    /**
     * @var string $packagistRoute
     */
    private $packagistRoute;
    /**
     * @var RouteParametersForBadgeCompiler
     */
    private $routeParametersForBadgeCompiler;

    /**
     * @param Router $router
     * @param $badges
     * @param string $packagist_route
     */
    public function __construct(Router $router, array $badges, array $allInBadges, $packagist_route = 'pugx_badge_packagist', RouteParametersForBadgeCompiler $routeParametersForBadgeCompiler)
    {
        $this->router = $router;
        $this->badges = $badges;
        $this->allInBadges = $allInBadges;
        $this->packagistRoute = $packagist_route;
        $this->routes = $this->router->getRouteCollection();
        $this->routeParametersForBadgeCompiler = $routeParametersForBadgeCompiler;
    }

    /**
     * @param $repository
     * @return array
     */
    public function generateAllSnippets($repository)
    {
        $snippets = array();
        $snippets['clip_all']['markdown'] = '';
        foreach ($this->badges as $badge) {
            $markdown = $this->generateMarkdown($badge, $repository);
            $snippets[$badge['name']] = array(
                'markdown'  => $markdown,
                'img'       => $this->generateImg($badge, $repository)
            );

            if (in_array($badge['name'], $this->allInBadges)) {
                $snippets['clip_all']['markdown'] .= ' '.$markdown;
            }
        }
        $snippets['clip_all']['markdown'] = trim($snippets['clip_all']['markdown']);
        $snippets['repository']['html'] = $repository;

        return $snippets;
    }

    /**
     * @param $badge
     * @param $repository
     * @return string
     */
    public function generateMarkdown($badge, $repository)
    {
        return sprintf(
            "[![%s](%s)](%s)",
            $badge['label'],
            $this->generateImg($badge, $repository),
            $this->generateRepositoryLink($repository)
        );
    }

    /**
     * @param $badge
     * @param $repository
     * @return string
     */
    public function generateImg($badge, $repository)
    {
        $badge['repository'] = $repository;
        $parameters = $this->routeParametersForBadgeCompiler->compile($badge);

        return $this->router->generate($badge['route'], $parameters, true);
    }

    /**
     * @param $repository
     * @return string
     */
    public function generateRepositoryLink($repository)
    {
        return $this->router->generate($this->packagistRoute, array('repository' => $repository), true);
    }

}
