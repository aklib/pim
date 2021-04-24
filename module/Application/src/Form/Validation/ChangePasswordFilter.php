<?php

	namespace Application\Form\Validation;

	use Zend\InputFilter\InputFilter;
	use ZfcUser\Options\AuthenticationOptionsInterface;

	class ChangePasswordFilter extends InputFilter {
		public function __construct(AuthenticationOptionsInterface $options) {
			$this->add([
				'name'       => 'newCredential',
				'required'   => true,
				'validators' => [
					[
						'name'    => 'StringLength',
						'options' => [
							'min' => 6,
						],
					],
				],
				'filters'    => [
					['name' => 'StringTrim'],
				],
			]);

			$this->add([
				'name'       => 'newCredentialVerify',
				'required'   => true,
				'validators' => [
					[
						'name'    => 'StringLength',
						'options' => [
							'min' => 6,
						],
					],
					[
						'name'    => 'identical',
						'options' => [
							'token' => 'newCredential'
						]
					],
				],
				'filters'    => [
					['name' => 'StringTrim'],
				],
			]);
		}
	}
