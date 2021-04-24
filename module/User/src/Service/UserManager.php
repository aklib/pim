<?php

namespace User\Service;

use Application\ServiceManager\Interfaces\EntityManagerAware;
use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Application\ServiceManager\Interfaces\ViewRendererAware;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FlorianWolters\Component\Core\StringUtils;
use InvalidArgumentException;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Math\Rand;
use Laminas\View\Renderer\PhpRenderer;
use Psr\Container\ContainerInterface;
use User\Entity\User;
use User\Repository\UserDao;

/*use Laminas\Mail;
use Laminas\Mail\Transport\SmtpOptions;*/

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager implements EntityManagerAware, ServiceManagerAware, ViewRendererAware
{
    /**
     * Entity manager.
     * @var EntityManager
     */
    private EntityManager $em;

    /**
     * Entity manager.
     * @var ContainerInterface
     */
    private ContainerInterface $sm;

    /**
     * PHP template renderer.
     * @var PhpRenderer
     */
    private PhpRenderer $viewRenderer;

    /**
     * Application config.
     * @var array
     */
//        private $config;

    /**
     * Checks whether an active user with given email address already exists in the database.
     * @param $email
     * @return bool
     */
    public function checkUserExists(string $email): bool
    {
        /** @var UserDao $repository */
        $repository = $this->getEntityManager()->getRepository(User::class);
        return $repository->exists('email', $email);
    }

    /**
     * Encrypt raw password
     * @param string $password
     * @return string
     */
    public function createPasswordHash(string $password): string
    {
        if (empty($password)) {
            throw new InvalidArgumentException('The password can not be empty');
        }
        if (!is_scalar($password)) {
            throw new InvalidArgumentException('The password must be a scalar value');
        }
        $bcrypt = new Bcrypt();
        return $bcrypt->create($password);
    }

    /**
     * @param $password
     * @return bool
     * @todo do encryption check stronger eg. length
     */
    public function isEncrypted($password): bool
    {
        return StringUtils::startsWith($password, '$2y$');
    }

    /**
     * Checks that the given password is correct.
     * @param $user
     * @param $password
     * @return bool
     */
    public function verifyPassword($user, $password): bool
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }

        return false;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }

    public function getServiceManager(): ContainerInterface
    {
        return $this->sm;
    }

    public function setServiceManager(ContainerInterface $sm): void
    {
        $this->sm = $sm;
    }

    public function getViewRenderer(): PhpRenderer
    {
        return $this->viewRenderer;
    }

    public function setViewRenderer(PhpRenderer $viewRenderer): void
    {
        $this->viewRenderer = $viewRenderer;
    }

    //======================== Not used yet. Needed for reset password|email action ========================

    /**
     * This method updates data of an existing user.
     */
    /* public function updateUser($user, $data)
     {
         // Do not allow to change user email if another user with such email already exits.
         if ($user->getEmail() != $data['email'] && $this->checkUserExists($data['email'])) {
             throw new \Exception("Another user with email address " . $data['email'] . " already exists");
         }

         $user->setEmail($data['email']);
         $user->setFullName($data['full_name']);
         $user->setStatus($data['status']);

         // Apply changes to database.
         $this->em->flush();

         return true;
     }*/

    /**
     * Generates a password reset token for the user. This token is then stored in database and
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is
     * directed to the Set Password page.
     * @param User $user
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function generatePasswordResetToken(User $user): string
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);

        // Encrypt the token before storing it in DB.
        $tokenHash = $this->createPasswordHash($token);

        // Save token to DB
        $user->setPasswordResetToken($tokenHash);
        // Save token creation date to DB.
        $user->setPasswordResetTokenCreationDate(new DateTime());
        // Apply changes to DB.
        $this->getEntityManager()->flush($user);
        return $token;
    }

    /**
     * Checks whether the given password reset token is a valid one.
     * @param User $user
     * @param $passwordResetToken
     * @return bool
     */
    public function validatePasswordResetToken(User $user, $passwordResetToken): bool
    {
        if ($user->getStatus()->getId() !== User::STATUS_ACTIVE) {
            return false;
        }

        // Check that token hash matches the token hash in our DB.
        $bcrypt = new Bcrypt();
        $tokenHash = $user->getPasswordResetToken();

        if (!$bcrypt->verify($passwordResetToken, $tokenHash)) {
            return false; // mismatch
        }

        // Check that token was created not too long ago.
        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        // for test
        //$tokenCreationDate->modify('-24 hours');

        $currentDate = new DateTime();
        $diff = $currentDate->diff($tokenCreationDate);
        if ($diff->y > 0 || $diff->m > 0 || $diff->d > 0) {
            return false; // expired
        }
        return true;
    }

    /**
     * This method sets new password by password reset token.
     */
    /*public function setNewPasswordByToken($email, $passwordResetToken, $newPassword)
    {
        if (!$this->validatePasswordResetToken($email, $passwordResetToken)) {
           return false;
        }

        // Find user with the given email.
        $user = $this->em->getRepository(User::class)
                ->findOneByEmail($email);

        if ($user==null || $user->getStatus() != User::STATUS_ACTIVE) {
            return false;
        }

        // Set new password for user
        $passwordHash = $this->createPassword($newPassword);
        $user->setPassword($passwordHash);

        // Remove password reset token
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);

        $this->em->flush();

        return true;
    }*/

    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    /* public function changePassword($user, $data)
     {
         $oldPassword = $data['old_password'];

         // Check that old password is correct
         if (!$this->validatePassword($user, $oldPassword)) {
             return false;
         }

         $newPassword = $data['new_password'];

         // Check password length
         if (strlen($newPassword)<6 || strlen($newPassword)>64) {
             return false;
         }

         // Set new password for user
         $passwordHash = $this->createPassword($newPassword);
         $user->setPassword($passwordHash);

         // Apply changes
         $this->em->flush();

         return true;
     }*/

}

