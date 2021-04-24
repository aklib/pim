<?php

namespace Application\Exception;

use InvalidArgumentException;

/**
 * Exception in the case that an entity was not found
 * while handling edit some entity
 */
class FormValidationException extends InvalidArgumentException
{

}
