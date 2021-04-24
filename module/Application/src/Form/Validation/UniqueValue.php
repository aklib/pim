<?php

/**
 *
 * UniqueValue.php
 *
 * @since 27.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Form\Validation;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\AbstractValidator;
use Zend\Soap\Exception\UnexpectedValueException;

class UniqueValue extends AbstractValidator {
    /**
     * Error constants
     */
    const ERROR_OBJECT_FOUND    = 'objectFound';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::ERROR_OBJECT_FOUND    => "An object matching '%value%' was found",
    ];

    protected $adapter;
    protected $method;
    protected $field;

    public function __construct($options = null) {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (empty($options['adapter'])) {
            throw new UnexpectedValueException("Adapter can't be empty");
        }
        if (empty($options['method'])) {
            throw new UnexpectedValueException("Adapter method can't be empty");
        }

        if (!is_object($options['adapter'])) {
            throw new UnexpectedValueException("Adapter must by an object");
        }

         parent::__construct($options);
         if(!method_exists ($this->getAdapter() , $this->getMethod())){
             throw new UnexpectedValueException(sprintf(
                    "Method '%s::%s' not exists", get_class($this->getAdapter()),$this->getMethod())
            );
         }
    }

    public function isValid($value) {
        $options = [
            'field' => $this->getField()
        ];
        $result = call_user_func([$this->getAdapter(), $this->getMethod()], $value, $options);
        if ($result === false || is_object($result)) {
            $this->error(self::ERROR_OBJECT_FOUND, $value);
            return false;
        }
        return true;
    }

    public function getAdapter() {
        return $this->adapter;
    }

    public function setAdapter($adapter) {
        $this->adapter = $adapter;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function getField() {
        return $this->field;
    }

    public function setField($field) {
        $this->field = $field;
    }
}
