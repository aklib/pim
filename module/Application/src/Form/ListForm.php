<?php
    /**
     * Class ViewConfiguration
     * @package Application\Form
     *
     * since: 31.08.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Form;

    use Laminas\Form\Form;

    class ListForm extends Form
    {


        /**
         * ListForm constructor.
         * @param null $name
         * @param array $options
         */
        public function __construct($name = null, $options = [])
        {
            parent::__construct($name, $options);
            $this->setName('listform');
            $this->setAttribute('id','listform');
            $this->setAttribute('method','get');
            $this->setAttribute('action','');
            $this->setAttribute('class','form');
        }
    }