<?php

namespace Category\Controller;


use Application\Controller\AbstractModuleController;
use Application\Form\EntityEdit;
use Category\Entity\Category;
use Category\Form\CategoryCreate;
use Exception;
use Laminas\Form\Form;
use Laminas\View\Model\ViewModel;

/**
 * This controller is responsible for categories management (adding, editing,
 */
class CategoryController extends AbstractModuleController
{
    /**
     * @param object|null $entity
     * @param Form|null $form
     * @param array|null $options
     * @return ViewModel
     * @throws Exception
     */
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
     */
    public function createAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        $category = new Category();
        /** @var EntityEdit $createForm */
        $createForm = $this->getServiceManager()->get(CategoryCreate::class);
        $createForm->createForm($category);

        return parent::editAction($category, $createForm);
    }

    /**
     * Deletes an category
     * @param object|null $entity
     * @param Form|null $form
     * @param array|null $options
     * @return ViewModel
     */
    public function deleteAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        return parent::deleteAction($entity, $form, $options);
    }
}