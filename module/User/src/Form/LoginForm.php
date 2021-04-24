<?php /** @noinspection ALL */

namespace User\Form;

use Application\Form\AbstractAwareForm;
use User\ModuleOptions;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class LoginForm extends AbstractAwareForm
{


    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('login-form');
        // Set POST method for this form
        $this->setAttribute('method', 'post');
    }

    /**
     * @param object|null $param
     */
    public function createForm(?object $entity = null): void
    {
        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements(): void
    {
        // Add "email" field
        $this->add([
            'type'    => 'text',
            'name'    => 'email',
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);

        // Add "password" field
        $this->add([
            'type'    => 'password',
            'name'    => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        // Add "remember_me" field
        $this->add([
            'type'    => 'checkbox',
            'name'    => 'remember_me',
            'options' => [
                'label' => 'Remember me',
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

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter(): void
    {
        // Create main input filter
        $inputFilter = $this->getInputFilter();

        // Add input for "email" field
        $inputFilter->add([
            'name'       => 'email',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'EmailAddress',
                    'options' => [
                        'allow'      => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);

        // Add input for "password" field
        $inputFilter->add([
            'name'       => 'password',
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

        // Add input for "remember_me" field
        $inputFilter->add([
            'name'       => 'remember_me',
            'required'   => false,
            'filters'    => [
            ],
            'validators' => [
                [
                    'name'    => 'InArray',
                    'options' => [
                        'haystack' => [0, 1],
                    ]
                ],
            ],
        ]);
    }
}

