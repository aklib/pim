<?php

/**
 *
 * AbstractForm.php
 *
 * @since  25.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Form;

use Acl\Service\AclService;
use Application\ServiceManager\Interfaces\AclAware;
use Application\ServiceManager\Interfaces\AuthenticationAware;
use Application\ServiceManager\Interfaces\EntityManagerAware;
use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Form;
use Laminas\I18n\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;
use User\Entity\User;

abstract class AbstractAwareForm extends Form implements ServiceManagerAware, EntityManagerAware, AuthenticationAware, AclAware
{
    private ContainerInterface $sm;
    private EntityManager $em;
    private ?User $user;
    private bool $initialized = false;
    private TranslatorInterface $translator;
    private AclService $acl;

    public function getCurrentUser(): User
    {
        return $this->user;
    }

    public function setCurrentUser(?User $user): void
    {
        $this->user = $user;
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

    public function isAjax()
    {
        return $this->getServiceManager()->get('Request')->isXmlHttpRequest();
    }

    /**
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * @param bool $initialized
     */
    public function setInitialized(bool $initialized): void
    {
        $this->initialized = $initialized;
    }

    protected function getAddOnIconHTML(string $icon, string $btnClass = 'btn-default'): string
    {
        return '<button class="btn ' . $btnClass . ' btn-clean btn-icon" type="button">
                        <i class="' . $icon . '"></i>
                    </button>';
    }

    protected function getAddOnCheckboxHTML(string $fieldName): string
    {
        return '<span class="input-group-text">
                       <label class="checkbox checkbox-outline checkbox-success">
                        <input type="hidden" name="' . $fieldName . '" value="0"/>
                        <input type="checkbox" name="' . $fieldName . '" value="1"/>
                        <span></span>
                       </label>
                      </span>';
    }

    public function getAcl(): AclService
    {
        return $this->acl;
    }

    public function setAcl(AclService $acl): void
    {
        $this->acl = $acl;
    }

    public function hasAcl(): bool
    {
        return true;
    }
}
