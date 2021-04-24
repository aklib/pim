<?php

namespace User\Validator;

use Laminas\Validator\Identical;
use Psr\Container\ContainerInterface;
use User\Entity\User;
use User\Service\UserManager;

/**
 * This validator is designed to check old passwords when they are changed.
 */
class PasswordValidator extends Identical
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = [
        'entityManager' => null,
        'user'          => null
    ];

    // Validation failure message IDs.
    public const PASSWORD_INVALID = 'passwordNotMatch';
    protected const NOT_SCALAR = 'notScalar';
    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_SCALAR       => "The password must be a scalar value",
        self::PASSWORD_INVALID => "The old password is incorrect. Could not set the new password",
    ];

    /**
     * Entity manager.
     * @var ContainerInterface
     */
    private ContainerInterface $sm;

    /**
     * Entity manager.
     * @var User
     */
    private User $user;

    /**
     * Constructor.
     * @param null $options
     * @noinspection UnusedConstructorDependenciesInspection
     */
    public function __construct($options = null)
    {
        // Set filter options (if provided).
        if (is_array($options)) {
            if (isset($options['sm'])) {
                $this->sm = $options['sm'];
            }
            if (isset($options['user'])) {
                $this->user = $options['user'];
            }
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if user exists.
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @param $value
     * @param null $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {

        if (!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        /** @var UserManager $userManager */
        $userManager = $this->sm->get(UserManager::class);
        $isValid = $userManager->verifyPassword($this->user, $value);
        // If there were an error, set error message.
        if (!$isValid) {
            $this->error(self::PASSWORD_INVALID);
        }
        // Return validation result.
        return $isValid;
    }
}

