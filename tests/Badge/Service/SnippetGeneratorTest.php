<?php

namespace App\Tests\Service;

use App\Badge\Service\RouteParametersForBadgeCompiler;
use App\Badge\Service\SnippetGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Router;
use Symfony\Component\VarDumper\VarDumper;

class SnippetGeneratorTest extends TestCase
{

    /**
     * @var Router
     */
    private $router;

    /**
     * @var SnippetGenerator
     */
    private $snippetGenerator;

    /**
     * @var RouteParametersForBadgeCompiler
     */
    private $routeCompiler;

    public function setUp()
    {
        $this->router = $this->createMock(Router::class);
        $this->routeCompiler = $this->createMock(RouteParametersForBadgeCompiler::class);
        $this->snippetGenerator = new SnippetGenerator($this->router, [], [], 'packagist_route', $this->routeCompiler);
    }

    public function test_generateImg()
    {
        $badge = [
            'name' => 'latest_stable_version',
            'label' => 'Latest Stable Version',
            'route' => 'pugx_badge_version_latest',
            'latest' => 'stable'
        ];

        $this->router->method('generate')
            ->with('pugx_badge_version_latest', ['param1' => 1, 'param2' => 'b'], true)
            ->willReturn('full/valid/route');

        $modifiedBadge = array_merge($badge, ['repository' => 'packagist/repository']);
        $this->routeCompiler->method('compile')->with($modifiedBadge)->willReturn(['param1' => 1, 'param2' => 'b']);
        $generatedImage = $this->snippetGenerator->generateImg($badge, 'packagist/repository');

        $this->assertEquals('full/valid/route',$generatedImage);
    }

    public function test_generateRepositoryLink()
    {
        $this->router->method('generate')
            ->with('packagist_route', ['repository' => 'packagist/repository'], true)
            ->willReturn('full/valid/route');

        $repositoryLink = $this->snippetGenerator->generateRepositoryLink('packagist/repository');
        $this->assertEquals('full/valid/route', $repositoryLink);
    }
}
