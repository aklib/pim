<?php

/**
 *
 * StatusEdit.php
 *
 * @since 26.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Form;

use Application\ServiceManager\Interfaces\Constant;
use Laminas\Validator\InArray;

class StatusEdit extends AbstractEntityForm
{
    protected function isPropertyShown($name): bool
    {
        $yes = parent::isPropertyShown($name);
        if(!$yes){
            return false;
        }
        switch($name){
            case 'status':
                return true;
        }
        return false;
    }

    protected function postCreateForm($entity = null): void
    {
        parent::postCreateForm($entity);
        $inputFilter = $this->getInputFilter();
        // Add input for "status" field
        $inputFilter->add([
            'name'     => 'status',
            'required' => true,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => InArray::class, 'options' => ['haystack' => [Constant::INACTIVE, Constant::ACTIVE]]]
            ],
        ]);


    }
}
