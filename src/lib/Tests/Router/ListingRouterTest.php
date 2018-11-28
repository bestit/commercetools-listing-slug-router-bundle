<?php

namespace BestIt\CtListingSlugRouter\Tests;

use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;
use BestIt\CtListingSlugRouter\Exception\ForbiddenCharsException;
use BestIt\CtListingSlugRouter\Repository\CategoryRepositoryInterface;
use BestIt\CtListingSlugRouter\Router\ListingRouter;
use Commercetools\Core\Model\Category\Category;
use Commercetools\Core\Model\Common\Context;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Tests the listing router
 *
 * @author chowanski <chowanski@bestit-online.de>
 * @category Tests
 * @package BestIt\CtListingSlugRouter\Tests
 */
class ListingRouterTest extends TestCase
{
    /**
     * The used repository.
     *
     * @var CategoryRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $repo = null;

    /**
     * The tested router.
     *
     * @var ListingRouter
     */
    private $router = null;

    /**
     * Returns a decoded category fixture
     *
     * @param string $filename
     *
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
     *
     * @param string $filename
     *
     * @return string
     */
    private function getFixture(string $filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->router = new ListingRouter($this->repo = static::createMock(CategoryRepositoryInterface::class));
    }

    /**
     * Checks the constants of the router.
     *
     * @return void
     */
    public function testConstants()
    {
        static::assertSame('best_it_frontend_listing_listing_index', ListingRouter::DEFAULT_ROUTE);
        static::assertSame(
            'BestIt\Frontend\ListingBundle\Controller\ListingController::indexAction',
            ListingRouter::DEFAULT_CONTROLLER
        );
    }

    /**
     * Test context property (getter / setter)
     *
     * @return void
     */
    public function testContextProperty()
    {
        $context = new RequestContext();

        $this->router->setContext($context);
        static::assertSame($context, $this->router->getContext());
    }

    /**
     * Test that generate method throws exception if slug param is missing
     *
     * @return void
     */
    public function testGenerateByRouteNameWithoutSlugParam()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->router->generate('best_it_frontend_listing_listing_index');
    }

    /**
     * Test that generate method creates route
     *
     * @return void
     */
    public function testGenerateRouteByName()
    {
        $this->router->setContext(new RequestContext());

        static::assertSame(
            '/foobar',
            $this->router->generate('best_it_frontend_listing_listing_index', ['slug' => 'foobar'])
        );
    }

    /**
     * Test that generate method creates route with query
     *
     * @return void
     */
    public function testGenerateRouteByNameWithQuery()
    {
        $this->router->setContext(new RequestContext());

        static::assertSame(
            '/foobar?best=it',
            $this->router->generate('best_it_frontend_listing_listing_index', ['slug' => 'foobar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route with query
     *
     * @return void
     */
    public function testGenerateRouteByNameWithQueryAndBaseUrl()
    {
        $this->router->setContext(new RequestContext('/app_dev.php'));

        static::assertSame(
            '/app_dev.php/foobar?best=it',
            $this->router->generate('best_it_frontend_listing_listing_index', ['slug' => 'foobar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObject()
    {
        $this->router->setContext(new RequestContext());

        $category = $this->getCategoryFixture('category.json');

        static::assertSame(
            '/haustechnik-ht-koerperpflege-mundpflege',
            $this->router->generate($category)
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObjectWithQuery()
    {
        $this->router->setContext(new RequestContext());

        $category = $this->getCategoryFixture('category.json');

        static::assertSame(
            '/haustechnik-ht-koerperpflege-mundpflege?foo=bar&best=it',
            $this->router->generate($category, ['foo' => 'bar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObjectWithQueryAndBaseUrl()
    {
        $this->router->setContext(new RequestContext('/app_dev.php'));

        $category = $this->getCategoryFixture('category.json');

        static::assertSame(
            '/app_dev.php/haustechnik-ht-koerperpflege-mundpflege?foo=bar&best=it',
            $this->router->generate($category, ['foo' => 'bar', 'best' => 'it'])
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

        $this->router->generate('foobar', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Test that generate method throws exception when network path is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForNetworkPath()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->router->generate('foobar', [], UrlGeneratorInterface::NETWORK_PATH);
    }

    /**
     * Test that generate method throws exception when relative path type is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForRelativePath()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->router->generate('foobar', [], UrlGeneratorInterface::RELATIVE_PATH);
    }

    /**
     * Test that generate method throws exception
     *
     * @return void
     */
    public function testGenerateThrowsNotFoundException()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->router->generate('foobar');
    }

    /**
     * Test get route collection
     *
     * @return void
     */
    public function testGetRouteCollection()
    {
        static::assertEquals(new RouteCollection(), $this->router->getRouteCollection());
    }

    /**
     * Checks the getter.
     *
     * @return void
     */
    public function testGetRouteDebugMessage()
    {
        static::assertSame($name = uniqid(), $this->router->getRouteDebugMessage($name));
    }

    /**
     * Checks if the required api is registered.
     *
     * @return void
     */
    public function testInterfaces()
    {
        static::assertInstanceOf(RouterInterface::class, $this->router);
        static::assertInstanceOf(VersatileGeneratorInterface::class, $this->router);
    }

    /**
     * Check if the parameter contains special characters
     *
     * @return void
     */
    public function testMatchFailedWithForbiddenCharsException()
    {
        $this->expectException(ForbiddenCharsException::class);

        $this->router->match('!"§');
    }

    /**
     * Test match method with unknown entity
     *
     * @return void
     */
    public function testMatchFailedWithExceptionMapping()
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->repo
            ->method('getCategoryBySlug')
            ->with('foobar')
            ->willThrowException(new CategoryNotFoundException());

        $this->router->match('foobar');
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

        $this->repo
            ->method('getCategoryBySlug')
            ->with(self::equalTo($pathInfo))
            ->willReturn($category);

        static::assertSame($expectedResponse, $this->router->match($pathInfo));
    }

    /**
     * Checks the default return of the method.
     *
     * @return void
     */
    public function testSupportsDefault()
    {
        static::assertFalse($this->router->supports(uniqid()));
    }

    /**
     * Checks the return of the method by category.
     *
     * @return void
     */
    public function testSupportsTrueByModel()
    {
        static::assertTrue($this->router->supports(new Category()));
    }

    /**
     * Checks the return of the method by route.
     *
     * @return void
     */
    public function testSupportsTrueByRoute()
    {
        static::assertTrue($this->router->supports(ListingRouter::DEFAULT_ROUTE));
    }
}
