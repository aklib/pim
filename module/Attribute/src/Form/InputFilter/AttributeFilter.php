<?php
    /**
     * Class AttributeFilter
     * @package Attribute\Form\InputFilter
     *
     * since: 11.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Form\InputFilter;


    use Application\ServiceManager\Interfaces\Constant;
    use Laminas\Filter\StringTrim;
    use Laminas\Filter\ToInt;
    use Laminas\Validator\InArray;

    class AttributeFilter
    {
        public function getAll(): array
        {
            return [
                [
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                [
                    'name'       => 'status',
                    'required'   => true,
                    'filters'    => [
                        ['name' => ToInt::class],
                    ],
                    'validators' => [
                        ['name' => InArray::class, 'options' => ['haystack' => [Constant::INACTIVE, Constant::ACTIVE]]]
                    ],
                ]
            ];
        }
    }