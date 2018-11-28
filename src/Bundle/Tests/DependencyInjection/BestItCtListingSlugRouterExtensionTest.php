<?php

namespace BestIt\CtListingSlugRouterBundle\Tests\DependencyInjection;

use BestIt\CtListingSlugRouter\Router\ListingRouter;
use BestIt\CtListingSlugRouterBundle\DependencyInjection\BestItCtListingSlugRouterExtension;
use BestIt\CtListingSlugRouterBundle\Tests\FakeRepository;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class BestItCtListingSlugRouterExtensionTest
 *
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtListingSlugRouterBundle\Tests\DependencyInjection
 */
class BestItCtListingSlugRouterExtensionTest extends AbstractExtensionTestCase
{
    /**
     * Returns the container extensions to test.
     *
     * @return BestItCtListingSlugRouterExtension[]
     */
    protected function getContainerExtensions(): array
    {
        return [new BestItCtListingSlugRouterExtension()];
    }

    /**
     * Optionally override this method to return an array that will be used as the minimal configuration for loading
     * the container extension under test, to prevent a test from failing because of a missing required
     * configuration value for the container extension.
     *
     * @return void
     */
    protected function getMinimalConfiguration(): array
    {
        $this->registerService(
            'best_it_ct_listing_slug_router.tests.fake_repository',
            FakeRepository::class
        );

        return [
            'repository' => 'best_it_ct_listing_slug_router.tests.fake_repository'
        ];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->load();
    }

    /**
     * Checks if there is an event listener for refreshing the change management.
     *
     * @return void
     */
    public function testChecksRouterServiceExistence()
    {
        static::assertContainerBuilderHasServiceDefinitionWithTag(
            'best_it.ct_listing_slug_router.router',
            'router',
            ['priority' => '%best_it.ct_listing_slug_router.priority%']
        );
    }

    /**
     * Checks the default values for the container.
     *
     * @return void
     */
    public function testDefaultParameters()
    {
        $defaults = [
            'controller' => ListingRouter::DEFAULT_CONTROLLER,
            'priority' => 0,
            'route' =>  ListingRouter::DEFAULT_ROUTE
        ];

        foreach ($defaults as $name => $value) {
            static::assertSame($value, $this->container->getParameter('best_it.ct_listing_slug_router.' . $name));
        }
    }

    /**
     * Checks if an exception is thrown, if there is no repo.
     *
     * @return void
     */
    public function testExceptionOnMissingRepo()
    {
        static::expectException(InvalidConfigurationException::class);

        $this->load(['repository' => '']);
    }

    /**
     * Checks the default values for the container.
     *
     * @return void
     */
    public function testParameterValues()
    {
        $this->load($values = [
            'controller' => uniqid(),
            'priority' => mt_rand(1, 10000),
            'route' => uniqid()
        ]);

        foreach ($values as $name => $value) {
            static::assertSame($value, $this->container->getParameter('best_it.ct_listing_slug_router.' . $name));
        }
    }

    /**
     * Checks if the repository is registered as an alias.
     *
     * @return void
     */
    public function testRepositoryAlias()
    {
        static::assertContainerBuilderHasAlias(
            'best_it.ct_listing_slug_router.listing_repository',
            'best_it_ct_listing_slug_router.tests.fake_repository'
        );
    }
}
