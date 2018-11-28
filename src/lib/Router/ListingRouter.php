<?php

namespace BestIt\CtListingSlugRouter\Router;

use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;
use BestIt\CtListingSlugRouter\Repository\CategoryRepositoryInterface;
use Commercetools\Core\Model\Category\Category;
use InvalidArgumentException;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listing router
 *
 * @author chowanski <chowanski@bestit-online.de>
 * @package BestIt\CtListingSlugRouter\Router
 */
class ListingRouter implements RouterInterface, VersatileGeneratorInterface
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
        string $route = self::DEFAULT_ROUTE
    ) {
        $this
            ->setController($controller)
            ->setRepository($repository)
            ->setRoute($route);
    }

    /**
     * Generates a route.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string $name
     * @param array $parameters
     * @param int $referenceType
     *
     * @return string
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        if ($referenceType != self::ABSOLUTE_PATH) {
            throw new RouteNotFoundException('Only `absolute path` is allowed for category route generation');
        }

        if (is_string($name)) {
            $slug = $this->getSlugByName($name, $parameters);
        } else {
            $slug = $this->getSlugByObject($name);
        }

        if (!$slug) {
            throw new RouteNotFoundException('Not category found for route ' . (string) $name);
        }

        $url = sprintf('%s/%s', $this->getContext()->getBaseUrl(), $slug);
        if ($query = http_build_query($parameters)) {
            $url .= sprintf('?%s', $query);
        }

        return $url;
    }

    /**
     * Returns the context.
     *
     * @return RequestContext
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
     * Returns the route collection.
     *
     * @return RouteCollection
     */
    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    /**
     * Returns the route debug message.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param mixed $name
     * @param array $parameters
     *
     * @return string
     */
    public function getRouteDebugMessage($name, array $parameters = [])
    {
        return (string) $name;
    }

    /**
     * Get category slug by name
     *
     * @param string $name
     * @param array $params
     *
     * @return string|null
     */
    private function getSlugByName(string $name, array &$params)
    {
        $slug = null;

        if ($name === $this->getRoute()) {
            if (array_key_exists('slug', $params)) {
                $slug = $params['slug'];
                unset($params['slug']); // we do not want to add this to query
            } else {
                throw new InvalidArgumentException('Missing param `slug` for category route generation');
            }
        }

        return $slug;
    }

    /**
     * Get category slug by object
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param mixed $object
     *
     * @return null|string
     */
    private function getSlugByObject($object)
    {
        $slug = null;

        if ($object instanceof Category) {
            $slug = ($value = $object->getSlug()) ? $value->getLocalized() : null;
        }

        return $slug;
    }

    /**
     * Matches path infos to a route.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string $pathInfo
     *
     * @return array
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
     * Sets the context.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param RequestContext $context
     *
     * @return ListingRouter
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
     *
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
     *
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
     *
     * @return ListingRouter
     */
    private function setRoute(string $route): ListingRouter
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Returns true if this router supports the given route.
     *
     * @param mixed $name
     *
     * @return bool
     */
    public function supports($name)
    {
        return $name instanceof Category || $name == $this->getRoute();
    }
}
