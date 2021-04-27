<?php
/**
 * Class Options
 * @package Application
 * since: 30.06.2020
 * author: alexej@kisselev.de
 */

namespace User;

use Application\Options\AbstractModuleOptions;
use Psr\Container\ContainerInterface;

class ModuleOptions extends AbstractModuleOptions
{
    /**
     * @var bool
     */
    private bool $csrfOn = false;
    /**
     * @var bool
     */
    private bool $resetPasswordOn = false;
    /**
     * @var bool
     */
    private bool $registrationOn = false;
    /**
     * @var bool
     */
    private bool $dispatchOn = true;
    /**
     * @var bool
     */
    private bool $allowGuest = true;


    private ContainerInterface $sm;

    private array $passwordOptions;

    /**
     * @return bool
     */
    public function isCsrfOn(): bool
    {
        return $this->csrfOn;
    }

    /**
     * @param bool $csrfOn
     * @return ModuleOptions
     */
    public function setCsrfOn(bool $csrfOn): ModuleOptions
    {
        $this->csrfOn = $csrfOn;
        return $this;
    }

    /**
     * @return bool
     */
    public function isResetPasswordOn(): bool
    {
        return $this->resetPasswordOn;
    }

    /**
     * @param bool $resetPasswordOn
     * @return ModuleOptions
     */
    public function setResetPasswordOn(bool $resetPasswordOn): ModuleOptions
    {
        $this->resetPasswordOn = $resetPasswordOn;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRegistrationOn(): bool
    {
        return $this->registrationOn;
    }

    /**
     * @param bool $registrationOn
     * @return ModuleOptions
     */
    public function setRegistrationOn(bool $registrationOn): ModuleOptions
    {
        $this->registrationOn = $registrationOn;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDispatchOn(): bool
    {
        return $this->dispatchOn;
    }

    /**
     * @param bool $dispatchOn
     * @return ModuleOptions
     */
    public function setDispatchOn(bool $dispatchOn): ModuleOptions
    {
        $this->dispatchOn = $dispatchOn;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowGuest(): bool
    {
        return $this->allowGuest;
    }

    /**
     * @param bool $allowGuest
     */
    public function setAllowGuest(bool $allowGuest): void
    {
        $this->allowGuest = $allowGuest;
    }

    /**
     * @return array
     */
    public function getPasswordOptions(): array
    {
        return $this->passwordOptions;
    }

    /**
     * @param array $passwordOptions
     */
    public function setPasswordOptions(array $passwordOptions): void
    {
        $this->passwordOptions = $passwordOptions;
    }
}
