<?php
    /**
     * Class ReorderForm
     * @package Attribute\Form
     *
     * since: 24.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Form;


    use Application\Form\AbstractAwareForm;
    use Laminas\Form\Form;

    class ReorderForm extends AbstractAwareForm
    {


        public function createForm(?object $entity = null): void
        {
            $this->setAttribute('method', 'post');
        }
    }