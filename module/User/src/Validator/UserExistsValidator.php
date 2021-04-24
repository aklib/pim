<?php

namespace User\Validator;

use Laminas\Validator\AbstractValidator;
use Psr\Container\ContainerInterface;
use User\Service\UserManager;

/**
 * This validator class is designed for checking if there is an existing user
 * with such an email.
 */
class UserExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected array $options = [
        'sm'   => null,
        'user' => null
    ];

    // Validation failure message IDs.
    protected const NOT_SCALAR = 'notScalar';
    protected const USER_EXISTS = 'userExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected array $messageTemplates = [
        self::NOT_SCALAR  => "The email must be a scalar value",
        self::USER_EXISTS => "Another user with such an email already exists"
    ];

    /**
     * Constructor.
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // Set filter options (if provided).
        if (is_array($options)) {
            if (isset($options['sm'])) {
                $this->options['sm'] = $options['sm'];
            }
            if (isset($options['user'])) {
                $this->options['user'] = $options['user'];
            }
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if user exists.
     * @param $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        /** @var ContainerInterface $sm */
        $sm = $this->options['sm'];
        /** @var UserManager $userManager */
        $userManager = $sm->get(UserManager::class);
        $exists = $userManager->checkUserExists($value);
        if ($this->options['user'] === null) {
            // new user
            $isValid = !$exists;
        } else {
            // existing
            $isValid = $exists;
            //!($this->options['user']->getEmail() !== $value && $exists);
        }

        // If there were an error, set error message.
        if (!$isValid) {
            $this->error(self::USER_EXISTS);
        }
        // Return validation result.
        return $isValid;
    }
}

