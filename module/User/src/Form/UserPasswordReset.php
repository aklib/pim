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
use User\ModuleOptions;

class UserPasswordReset extends AbstractAwareForm
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('password-reset-form');
        // Set POST method for this form
        $this->setAttribute('method', 'post');
    }


    public function createForm(?object $entity = null): void
    {
        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements(): void
    {
        // Add "email" field
        $this->add([
            'type'    => 'email',
            'name'    => 'email',
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);


        /** @var ModuleOptions $options */
        $options = $this->getServiceManager()->get(ModuleOptions::class);
        if ($options->isCsrfOn()) {
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
        }

        // Add the Submit button
        $this->add([
            'type'       => 'submit',
            'name'       => 'submit',
            'attributes' => [
                'value' => 'Sign in',
                'id'    => 'submit',
            ],
        ]);
    }

    private function addInputFilter(): void
    {
        // Create main input filter
        $inputFilter = $this->getInputFilter();

        // Add input for "email" field
        $inputFilter->add([
            'name'     => 'email',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            /*'validators' => [
                [
                    'name'    => 'EmailAddress',
                    'options' => [
                        'useMxCheck' => false,
                    ],
                ],
            ],*/
        ]);
    }
}