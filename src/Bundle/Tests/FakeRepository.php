<?php

namespace BestIt\CtListingSlugRouterBundle\Tests;

use BadMethodCallException;
use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;
use BestIt\CtListingSlugRouter\Repository\CategoryRepositoryInterface;

/**
 * Fakes the existence of a class.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtListingSlugRouterBundle
 * @subpackage Tests
 * @version $id$
 */
class FakeRepository implements CategoryRepositoryInterface
{
    /**
     * Get category by slug
     *
     * @param string $slug
     * @param bool $exceptionOnMiss Should an exception be thrown if the category is not found.
     * @return mixed
     * @throws CategoryNotFoundException
     */
    public function getCategoryBySlug(string $slug, bool $exceptionOnMiss = true)
    {
        throw new BadMethodCallException('Not yet implemented.');
    }
}
