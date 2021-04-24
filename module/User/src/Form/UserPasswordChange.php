<?php
/**
 * Class UserEdit
 * @package User\Form
 * since: 05.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Form;


use Application\Form\AbstractEntityForm;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use User\Validator\PasswordValidator;

class UserPasswordChange extends AbstractEntityForm
{
    protected function isPropertyShown($name): bool
    {
        switch ($name) {
            case 'password':
                return true;
        }
        return false;
    }

    protected function postCreateForm($entity = null): void
    {
        parent::postCreateForm($entity);


        /** @var Element $passwordElement */
        $passwordElement = $this->get('password');
        $passwordElement->setLabel('Old Password');

        $passwordNewElement = clone $passwordElement;
        $passwordNewElement->setName('new_password');
        $passwordNewElement->setLabel('New Password');
        $this->add($passwordNewElement);

        $passwordNewConfirmElement = clone $passwordElement;
        $passwordNewConfirmElement->setName('confirm_new_password');
        $passwordNewConfirmElement->setLabel('Confirm new Password');
        $this->add($passwordNewConfirmElement);


        /** @var InputFilterInterface $inputFilter */
        $inputFilter = $this->getInputFilter();

        // Add input for "password" field
        $inputFilter->add([
            'name'       => 'password',
            'required'   => true,
            'filters'    => [
            ],
            'validators' => [
                [
                    'name'    => PasswordValidator::class,
                    'options' => [
                        'token' => 'password',
                        'user'  => $entity,
                        'sm'    => $this->getServiceManager()
                    ],
                ],
            ],
        ]);

        // Add input for "confirm_password" field
        if ($this->has('confirm_new_password')) {
            $inputFilter->add([
                'name'       => 'confirm_new_password',
                'required'   => true,
                'filters'    => [
                ],
                'validators' => [
                    [
                        'name'    => Identical::class,
                        'options' => [
                            'token' => 'new_password',
                        ],
                    ],
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);
        }
    }

}