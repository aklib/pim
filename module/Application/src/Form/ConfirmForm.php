<?php

/**
 *
 * ConfirmForm.php
 *
 * @since 28.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Form;

use Application\Entity\AppStatus;
use Laminas\Validator\InArray;

class ConfirmForm extends AbstractEntityForm {

    protected function isPropertyShown($name): bool
    {
        return false;
    }

    protected function postCreateForm($entity = null): void
    {
        parent::postCreateForm($entity);
        /*$inputFilter = $this->getInputFilter();
        // Add input for "status" field
        $inputFilter->add([
            'name'     => 'status',
            'required' => true,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name'=>InArray::class, 'options'=>['haystack'=>[AppStatus::_NEW, AppStatus::INACTIVE, AppStatus::ACTIVE]]]
            ],
        ]);*/


    }

}
