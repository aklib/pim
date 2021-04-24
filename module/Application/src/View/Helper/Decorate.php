<?php

/**
 * 
 * Decorate.php
 * 
 * @since 19.12.2019
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

use InvalidArgumentException;
use function call_user_func_array;
use function is_callable;

class Decorate extends AbstractHelperAware {

    public function __invoke($object, $method, $args = []) {
        if (!is_object($object)) {
            throw new InvalidArgumentException(sprintf(
                            '%s expects parameter to be object, "%s" given', __METHOD__, (get_debug_type($object))
            ));
        }
        $class = get_class($object) . 'Decorator';
        if ($this->getServiceManager()->has($class)) {
            $decorator = $this->getServiceManager()->get($class);
            $decorator->setObject($object);
            return call_user_func_array([$decorator, $method], (array) $args);
        }
        if (is_callable([$object, $method])) {
            return call_user_func_array([$object, $method], (array) $args);
        }
        return '';
    }
}
