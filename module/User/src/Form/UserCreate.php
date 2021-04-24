<?php
/**
 * Class UserEdit
 * @package User\Form
 * since: 05.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Form;


use Application\Form\Factory\FormElementFactory;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\Hostname;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use User\ModuleOptions;
use User\Validator\UserExistsValidator;

class UserCreate extends UserEdit
{
    protected function isPropertyShown($name): bool
    {
        switch ($name) {
            case 'passwordResetToken':
            case 'passwordResetTokenCreationDate':
                return false;
            case 'email':
            case 'password':
                return true;
        }
        $yes = parent::isPropertyShown($name);
        if (!$yes) {
            return false;
        }
        return true;
    }

    protected function postCreateForm($entity = null): void
    {
        parent::postCreateForm($entity);

        // create custom field to create companies for the new user
        /** @var FormElementFactory $factory */
        $factory = $this->getServiceManager()->get(FormElementFactory::class);
        $elementOptions = $factory->getElementOptions();

        $elementOptions['extended']['info'] = 'an advertiser and publisher companies will be created';
        $elementOptions['extended']['prepend'] = $this->getAddOnCheckboxHTML('create_companies');

        $element = new Text('create_companies_name', $elementOptions);
        $element->setLabel('Create Companies');
        $element
            ->setAttribute('class', 'form-control')
            ->setAttribute('placeholder', 'company name');
        $this->add($element);

        /** @var InputFilterInterface $inputFilter */
        $inputFilter = $this->getInputFilter();
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
                        'allow'      => Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
                [
                    'name'    => UserExistsValidator::class,
                    'options' => [
                        'sm'   => $this->getServiceManager(),
                        'user' => null
                    ],
                ],
            ],
        ]);
        // Add input for "password" field
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $this->getServiceManager()->get(ModuleOptions::class);
        $inputFilter->add([
            'name'       => 'password',
            'required'   => true,
            'filters'    => [
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => $moduleOptions->getPasswordOptions(),
                ],
            ],
        ]);

        // Add input for "confirm_password" field
        if ($this->has('confirm_password')) {
            $inputFilter->add([
                'name'       => 'confirm_password',
                'required'   => true,
                'filters'    => [
                ],
                'validators' => [
                    [
                        'name'    => Identical::class,
                        'options' => [
                            'token' => 'old_password',
                        ],
                    ],
                ],
            ]);
        }
    }
}
