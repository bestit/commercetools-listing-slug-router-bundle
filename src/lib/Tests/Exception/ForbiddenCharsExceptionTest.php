<?php

declare(strict_types=1);

namespace BestIt\CtListingSlugRouter\Tests\Exception;

use BestIt\CtListingSlugRouter\Exception\ForbiddenCharsException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ForbiddenCharsExceptionTest
 *
 * @author Georgi Damyanov <georgi.damyanov@bestit-online.at>
 * @package BestIt\CtListingSlugRouter\Tests\Exception
 */
class ForbiddenCharsExceptionTest extends TestCase
{

    /**
     * Tests that the forbidden chars exception is not found
     *
     * @return void
     */
    public function testType()
    {
        static::assertInstanceOf(NotFoundHttpException::class, new ForbiddenCharsException());
    }
}
