<?php

namespace BestIt\CtListingSlugRouterBundle\Tests;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;

/**
 * Class FakeRepositoryTest
 * @author blange <lange@bestit-online.de>
 * @category Tests
 * @package BestIt\CtListingSlugRouterBundle
 * @subpackage Tests
 * @version $id$
 */
class FakeRepositoryTest extends TestCase
{
    /**
     * The fake repo.
     * @var FakeRepository
     */
    private $fixture = null;

    /**
     * Sets up the test.
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new FakeRepository();
    }

    /**
     * Checks the exception call.
     */
    public function testGetCategoryBySlug()
    {
        static::expectException(BadMethodCallException::class);

        $this->fixture->getCategoryBySlug(uniqid());
    }
}
