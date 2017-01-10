<?php

namespace BestIt\CtListingSlugRouter\Router;

use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;
use BestIt\CtListingSlugRouter\Repository\CategoryRepositoryInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listing router
 *
 * @author chowanski <chowanski@bestit-online.de>
 * @package BestIt\CtListingSlugRouter
 * @subpackage Router
 * @version $id$
 */
class ListingRouter implements RouterInterface
{
    /**
     * The default controller.
     *
     * @var string
     */
    const DEFAULT_CONTROLLER = 'BestIt\Frontend\ListingBundle\Controller\ListingController::indexAction';

    /**
     * The default route.
     *
     * @var string
     */
    const DEFAULT_ROUTE = 'best_it_frontend_listing_listing_index';

    /**
     * The logical/full name for the used controller.
     *
     * @var string
     */
    private $controller = '';

    /**
     * The repository to fetch categories by slug.
     *
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * The used route name for this router.
     *
     * @var string
     */
    private $route = '';

    /**
     * The request context
     *
     * @var RequestContext
     */
    private $context;

    /**
     * ListingRouter constructor.
     *
     * @param CategoryRepositoryInterface $repository
     * @param string $controller
     * @param string $route
     */
    public function __construct(
        CategoryRepositoryInterface $repository,
        string $controller = self::DEFAULT_CONTROLLER,
        $route = self::DEFAULT_ROUTE
    ) {
        $this
            ->setController($controller)
            ->setRepository($repository)
            ->setRoute($route);
    }

    /**
     * @inheritdoc
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        throw new RouteNotFoundException('Not supported by ListingRouter');
    }

    /**
     * @inheritdoc
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * Returns the logical/full name for the used controller.
     *
     * @return string
     */
    private function getController(): string
    {
        return $this->controller;
    }

    /**
     * Returns the repository to fetch categories by slug.
     *
     * @return CategoryRepositoryInterface
     */
    private function getRepository(): CategoryRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Returns the used route name for this router.
     *
     * @return string
     */
    private function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @inheritdoc
     */
    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    /**
     * @inheritdoc
     */
    public function match($pathInfo): array
    {
        try {
            $category = $this->getRepository()->getCategoryBySlug(trim($pathInfo, '/'));
        } catch (CategoryNotFoundException $e) {
            throw new ResourceNotFoundException('Not category found for slug ' . $pathInfo);
        }

        return [
            '_controller' => $this->getController(),
            '_route' => $this->getRoute(),
            'category' => $category,
        ];
    }

    /**
     * @inheritdoc
     */
    public function setContext(RequestContext $context): ListingRouter
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Sets the logical/full name for the used controller.
     *
     * @param string $controller
     * @return ListingRouter
     */
    private function setController(string $controller): ListingRouter
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Sets the repository to fetch categories by slug.
     *
     * @param CategoryRepositoryInterface $repository
     * @return ListingRouter
     */
    private function setRepository(CategoryRepositoryInterface $repository): ListingRouter
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Sets the used route name for this router.
     *
     * @param string $route
     * @return ListingRouter
     */
    private function setRoute(string $route): ListingRouter
    {
        $this->route = $route;

        return $this;
    }
}
