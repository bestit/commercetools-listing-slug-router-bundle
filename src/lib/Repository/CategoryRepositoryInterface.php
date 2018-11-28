<?php

namespace BestIt\CtListingSlugRouter\Repository;

use BestIt\CtListingSlugRouter\Exception\CategoryNotFoundException;

/**
 * Repository to get category with a slug.
 *
 * @author chowanski <chowanski@bestit-online.de>
 * @package BestIt\CtListingSlugRouter\Repository
 */
interface CategoryRepositoryInterface
{
    /**
     * Get category by slug
     *
     * @throws CategoryNotFoundException
     *
     * @param string $slug
     * @param bool $exceptionOnMiss Should an exception be thrown if the category is not found.
     *
     * @return mixed
     */
    public function getCategoryBySlug(string $slug, bool $exceptionOnMiss = true);
}
