<?php

namespace Attribute\Controller;

use Application\Controller\AbstractModuleController;
use Application\Form\EntityEdit;
use Attribute\Entity\AttributeTab;
use Exception;
use Laminas\Form\Form;
use Laminas\View\Model\ViewModel;

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's own password).
 */
class TabController extends AbstractModuleController
{

    public function editAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        return parent::editAction($entity, $form, $options);
    }

    /**
     * @param object|null $entity
     * @param Form|null $form
     * @param array|null $options
     * @return ViewModel
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function createAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        $tab = new AttributeTab();
        /** @var EntityEdit $createForm */
        $createForm = $this->getServiceManager()->get(EntityEdit::class);
        $createForm->createForm($tab);

        return $this->editAction($tab, $createForm);
    }

}


