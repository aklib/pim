<?php

    namespace Attribute\Controller;


    use Application\Controller\AbstractModuleController;
    use Application\Form\EntityEdit;
    use Application\ServiceManager\Interfaces\Constant;
    use Application\View\Manager\ViewManager;
    use Attribute\Entity\Attribute;
    use Attribute\Entity\AttributeValue;
    use Attribute\Form\AttributeEdit;
    use Attribute\Form\ReorderForm;
    use Attribute\Repository\AttributeDao;
    use Attribute\Repository\AttributeValueDao;
    use Doctrine\ORM\OptimisticLockException;
    use Doctrine\ORM\ORMException;
    use Exception;
    use Laminas\Form\Form;
    use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
    use Laminas\View\Model\ViewModel;

    /**
     * This controller is responsible for user management (adding, editing,
     */
    class AttributeController extends AbstractModuleController
    {
        protected function postValidateFormData(array $data, object $entity = null): array
        {
            if (array_key_exists('attributeOptions', $data) && is_array($data['attributeOptions'])) {
                $count = 1;
                foreach ($data['attributeOptions'] as &$option) {
                    $option['sortOrder'] = $count++;
                }
            }


//    dump($data, $this->getRequest()->getPost());die;
            return parent::postValidateFormData($data, $entity); // TODO: Change the autogenerated stub
        }

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
         * @noinspection PhpUnused
         */
        public function createAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
        {
            $attribute = new Attribute();
            /** @var EntityEdit $createForm */
            $createForm = $this->getServiceManager()->get(AttributeEdit::class);
            $createForm->createForm($attribute);

            return $this->editAction($attribute, $createForm);
        }


        public function reorderAction(): ViewModel
        {
            /** @var AttributeDao $dao */
            $dao = $this->getEntityManager()->getRepository(Attribute::class);
            $attributes = $dao->getNamedQueryResult(Constant::NAMED_QUERY_ATTRIBUTES);
            /** @var ViewManager $manager */
            $manager = $this->getServiceManager()->get(ViewManager::class);
            $viewModel = $manager->createViewModel();
            $form = $this->getServiceManager()->get(ReorderForm::class);
            $this->layout()->setVariable('form', $form);
            $viewModel->setVariable('attributes', $attributes);
            if ($this->getRequest()->isPost()) {
                $attributeMap = [];
                /** @var Attribute $attribute */
                foreach ($attributes as $attribute) {
                    $attributeMap[$attribute->getId()] = $attribute;
                }
                $orders = array_flip(array_map('intval', $this->params()->fromPost('attribute')));

                /** @var Attribute $attribute */
                foreach ($attributes as $attribute) {
                    $order = $orders[$attribute->getId()] + 100;
                    $attribute->setSortOrder($order);
                }
                $submit = $this->params()->fromPost('submit');
                if ($submit === 'back') {
                    $url = $this->getURL('list', 'attribute', null);
                } else {
                    $url = $this->urlx();
                }
                /** @var AttributeDao $dao */
                $dao = $this->getEntityManager()->getRepository(Attribute::class);
                try {
                    $dao->doSave($attributes);
                    $this->addMessage("The attribute order has been successfully saved", FlashMessenger::NAMESPACE_SUCCESS);
                    return $this->redirect()->toUrl($url);

                } catch (OptimisticLockException $e) {
                } catch (ORMException $e) {
                }
                $this->addMessage("#generic: request error message", FlashMessenger::NAMESPACE_ERROR);
                return $this->redirect()->toUrl($url);
            }
            return $viewModel;
        }

        /**
         * Deletes an attribute
         * @param object|null $entity
         * @param Form|null $form
         * @param array|null $options
         * @return ViewModel
         */
        public function deleteAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
        {
            if (!$this->getRequest()->isPost()) {
                $viewModel = parent::deleteAction();
                $id = (int)$this->params()->fromRoute('id');
                /** @var AttributeValueDao $dao */
                $dao = $this->getEntityManager()->getRepository(AttributeValue::class);
                $countValues = $dao->countAttributeValues($id);
                if ($countValues > 0) {
                    $viewModel->setVariable('notice', "The attribute has $countValues values");
                } else {
                    $viewModel->setVariable('notice', 'The attribute has no values');
                }
                return $viewModel;
            }
            return parent::deleteAction($entity, $form, $options);
        }
    }