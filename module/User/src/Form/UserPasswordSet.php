<?php
/**
 * Class UserPasswordReset
 * @package User\Form
 *
 * since: 18.01.2021
 * author: alexej@kisselev.de
 */

namespace User\Form;


use Application\Form\AbstractAwareForm;

class UserPasswordSet extends AbstractAwareForm
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('password-set-form');
        // Set POST method for this form
        $this->setAttribute('method', 'post');
    }


    public function createForm(): void
    {
        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements(): void
    {
        // Add "new_password" field
        $this->add([
            'type'    => 'password',
            'name'    => 'new_password',
            'options' => [
                'label' => 'New Password',
            ],
        ]);

        // Add "confirm_new_password" field
        $this->add([
            'type'    => 'password',
            'name'    => 'confirm_new_password',
            'options' => [
                'label' => 'Confirm new password',
            ],
        ]);

        // Add the CSRF field
        $this->add([
            'type'    => 'csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'       => 'submit',
            'name'       => 'submit',
            'attributes' => [
                'value' => 'Submit Password'
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter(): void
    {
        // Create main input filter
        $inputFilter = $this->getInputFilter();
        if ($inputFilter === null) {
            // can not be
            return;
        }
        // Add input for "new_password" field
        $inputFilter->add([
            'name'       => 'new_password',
            'required'   => true,
            'filters'    => [
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 6,
                        'max' => 64
                    ],
                ],
            ],
        ]);

        // Add input for "confirm_new_password" field
        $inputFilter->add([
            'name'       => 'confirm_new_password',
            'required'   => true,
            'filters'    => [
            ],
            'validators' => [
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'new_password',
                    ],
                ],
            ],
        ]);
    }
}
