<?php

namespace BestIt\CtListingSlugRouter\Tests;

use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;
use BestIt\CtListingSlugRouter\Repository\CategoryRepositoryInterface;
use BestIt\CtListingSlugRouter\Router\ListingRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Tests the listing router
 *
 * @author chowanski <chowanski@bestit-online.de>
 * @category Tests
 * @package BestIt\CtListingSlugRouter
 * @subpackage Router
 * @version $id$
 */
class ListingRouterTest extends TestCase
{
    /**
     * Test context property (getter / setter)
     *
     * @return void
     */
    public function testContextProperty()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $context = new RequestContext();

        $router = new ListingRouter($categoryRepository);
        $router->setContext($context);
        static::assertSame($context, $router->getContext());
    }

    /**
     * Test that generate method throws exception
     *
     * @return void
     */
    public function testGenerateThrowsException()
    {
        $this->expectException(RouteNotFoundException::class);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->generate('foobar');
    }

    /**
     * Test get route collection
     *
     * @return void
     */
    public function testGetRouteCollection()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        static::assertEquals(new RouteCollection(), $router->getRouteCollection());
    }

    /**
     * Test match method with unknown entity
     *
     * @return void
     */
    public function testMatchFailed()
    {
        $this->expectException(ResourceNotFoundException::class);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->method('getCategoryBySlug')->willThrowException(new CategoryNotFoundException());

        $router = new ListingRouter($categoryRepository);
        $router->match('foobar');
    }

    /**
     * Test match method with known entity and get expected response
     *
     * @return void
     */
    public function testMatchSuccessfully()
    {
        $pathInfo = 'foobar';
        $category = 'Categorie FOOBAR';
        $expectedResponse = [
            '_controller' => ListingRouter::DEFAULT_CONTROLLER,
            '_route' => ListingRouter::DEFAULT_ROUTE,
            'category' => $category,
        ];

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->method('getCategoryBySlug')
            ->with(self::equalTo($pathInfo))
            ->willReturn($category);

        $router = new ListingRouter($categoryRepository);
        static::assertSame($expectedResponse, $router->match($pathInfo));
    }
}
