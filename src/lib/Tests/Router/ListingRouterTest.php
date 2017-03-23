<?php

namespace BestIt\CtListingSlugRouter\Tests;

use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;
use BestIt\CtListingSlugRouter\Repository\CategoryRepositoryInterface;
use BestIt\CtListingSlugRouter\Router\ListingRouter;
use Commercetools\Core\Model\Category\Category;
use Commercetools\Core\Model\Common\Context;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * Returns a decoded category fixture
     * @param string $filename
     * @return Category
     */
    private function getCategoryFixture(string $filename)
    {
        return Category::fromArray(
            json_decode(file_get_contents($this->getFixture($filename)), true),
            Context::of()->setLocale('de')->setLanguages(['de'])
        );
    }

    /**
     * Returns the fixture
     * @param string $filename
     * @return string
     */
    private function getFixture(string $filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . $filename;
    }

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
     * Test that generate method throws exception if slug param is missing
     *
     * @return void
     */
    public function testGenerateByRouteNameWithoutSlugParam()
    {
        $this->expectException(InvalidArgumentException::class);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->generate('best_it_frontend_listing_listing_index');
    }

    /**
     * Test that generate method creates route
     *
     * @return void
     */
    public function testGenerateRouteByName()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $router = new ListingRouter($categoryRepository);
        $router->setContext(new RequestContext());

        static::assertSame(
            '/foobar',
            $router->generate('best_it_frontend_listing_listing_index', ['slug' => 'foobar'])
        );
    }

    /**
     * Test that generate method creates route with query
     *
     * @return void
     */
    public function testGenerateRouteByNameWithQuery()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->setContext(new RequestContext());

        static::assertSame(
            '/foobar?best=it',
            $router->generate('best_it_frontend_listing_listing_index', ['slug' => 'foobar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route with query
     *
     * @return void
     */
    public function testGenerateRouteByNameWithQueryAndBaseUrl()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->setContext(new RequestContext('/app_dev.php'));

        static::assertSame(
            '/app_dev.php/foobar?best=it',
            $router->generate('best_it_frontend_listing_listing_index', ['slug' => 'foobar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObject()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->setContext(new RequestContext());

        $category = $this->getCategoryFixture('category.json');

        static::assertSame(
            '/haustechnik-ht-koerperpflege-mundpflege',
            $router->generate($category)
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObjectWithQuery()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->setContext(new RequestContext());

        $category = $this->getCategoryFixture('category.json');

        static::assertSame(
            '/haustechnik-ht-koerperpflege-mundpflege?foo=bar&best=it',
            $router->generate($category, ['foo' => 'bar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObjectWithQueryAndBaseUrl()
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->setContext(new RequestContext('/app_dev.php'));

        $category = $this->getCategoryFixture('category.json');

        static::assertSame(
            '/app_dev.php/haustechnik-ht-koerperpflege-mundpflege?foo=bar&best=it',
            $router->generate($category, ['foo' => 'bar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method throws exception when absolute url is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForAbsoluteUrl()
    {
        $this->expectException(RouteNotFoundException::class);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->generate('foobar', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Test that generate method throws exception when network path is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForNetworkPath()
    {
        $this->expectException(RouteNotFoundException::class);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->generate('foobar', [], UrlGeneratorInterface::NETWORK_PATH);
    }

    /**
     * Test that generate method throws exception when relative path type is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForRelativePath()
    {
        $this->expectException(RouteNotFoundException::class);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $router = new ListingRouter($categoryRepository);
        $router->generate('foobar', [], UrlGeneratorInterface::RELATIVE_PATH);
    }

    /**
     * Test that generate method throws exception
     *
     * @return void
     */
    public function testGenerateThrowsNotFoundException()
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
