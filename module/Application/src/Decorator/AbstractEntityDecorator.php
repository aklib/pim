<?php
    /** @noinspection PhpUnused */

    /**
 * AbstractDecorator.php
 *
 * @since 09.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Decorator;

use Application\ServiceManager\AbstractAwareContainer;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;

abstract class AbstractEntityDecorator extends AbstractAwareContainer
{
    private object $__object;

    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], (array)$args);
        }
        if (!is_callable([$this->__object, $method])) {
            return '';
        }
        $value = call_user_func_array([$this->__object, $method], (array)$args);

        if ($value instanceof Collection) {
            $array = [];
            foreach ($value as $val) {
                if (method_exists($val, '__toString')) {
                    $array[] = $val->__toString();
                }
            }
            return implode(', ', $array);
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return $value->__toString();
        }
        return $value;
    }

    /**
     * Sets decorated class instance
     * @param $object
     */
    public function setObject(object $object): void
    {
        if(!is_object($object)){
            throw new InvalidArgumentException(sprintf(
                '%s expects parameter to be object, "%s" given', __METHOD__, (get_debug_type($object))
            ));
        }
        $this->__object = $object;
    }

    /**
     * Gets decorated object instance (entity)
     * @return object
     */
    public function getObject(): object {
        return $this->__object;
    }
}
