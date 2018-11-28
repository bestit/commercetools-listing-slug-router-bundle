<?php

namespace BestIt\CtListingSlugRouter\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Exception for forbidden characters.
 *
 * @author Georgi Damyanov <georgi.damyanov@bestit-online.at>
 * @package BestIt\CtListingSlugRouter\Exception
 */
class ForbiddenCharsException extends NotFoundHttpException
{
}
