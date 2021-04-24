<?php

namespace Acl\Controller;

use Acl\Entity\AclResource;
use Acl\Repository\AclResourceDao;
use Acl\Service\AclService;
use Application\Form\ListForm;
use Application\View\Model\Tab\TabFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Laminas\Form\Form;
use Laminas\Http\Response;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

class MVCController extends AbstractAclController
{

    protected array $black_list = [
        'notFoundAction',
        'getMethodFromAction',
        'getAction',
        'setAction'
    ];


    /**
     * {@inheritdoc}
     * @param ViewModel|null $viewModel
     * @param array $options
     * @return ViewModel
     * @throws Exception
     */
    public function listAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {
        if (is_null($viewModel)) {
            $options['configurable'] = false;
            $viewModel = parent::listAction(null, $options);
        }
        // disable configure view button
        $this->layout()->setVariable('aclResource', null);
        /** @var ListForm $form */
        $form = $this->getServiceManager()->get(ListForm::class);
        $form->setAttribute('method', 'post');
        $form->setAttribute('action',
            $this->url()->fromRoute('default/admin', ['action' => 'edit', 'controller' => $this->params('controller')])
        );
        $this->layout()->setVariable('form', $form);


        /** @var AclService $acl */
        $savedResourceMap = $this->getSavedResourceRoleMap();
        $controllerResourceMap = $this->getControllerResourceMap();
        $getRoleIdMap = $this->getRoleIdMap(true); //array('developer', 'admin')
        foreach ($controllerResourceMap as $controller => $privileges) {
            if (empty($privileges)) {
                continue;
            }
            $resourceLower = strtolower($controller);
            $values = (array)($savedResourceMap[$resourceLower] ?? []);
            $content = new ViewModel([
                'controllerActions' => $privileges,
                'roles'             => $getRoleIdMap, //array('developer', 'admin'),
                'controller'        => $controller,
                'controllerClass'   => $resourceLower,
                'values'            => $values
            ]);

            $content->setTemplate('acl/partial/controller');
            $hasError = false;// new or removed controller action
            if (!isset($savedResourceMap[$resourceLower][1])) {
                //controller only
                $hasError = true;
            }

            $tab = TabFactory::addTab([
                'title'   => $controller,
                'useAcl'  => false,
                'content' => $content
            ]);

            $tab->addClass('flex-column', true);
            if ($hasError) {
                $tab->addClass('alert alert-custom alert-light-warning', true);
            }
        }
        TabFactory::setOrientation(TabFactory::ORIENTATION_VERTICAL);
        TabFactory::addContainerClass('nav-pills');
        //TabFactory::getViewModel()->setTemplate('default/tab/tab-flex-vertical');
        $viewModel->addChild(TabFactory::getViewModel(), 'tabs');
        return $viewModel;
    }

    /**
     * Find elements in AclResource collection by unique key
     * @param Collection $collection
     * @param string $resource
     * @param string $privilege
     * @return Collection
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    private function filterCollection(Collection $collection, string $resource, string $privilege): Collection
    {
        $type = $this->getResourceType();
        return $collection->filter(static function (AclResource $element) use ($type, $resource, $privilege) {
            // filter by unique key
            if ($element->getType() === $type && $element->getResource() === $resource && $element->getPrivilege() === $privilege) {
                return $element;
            }
        });
    }

    /**
     * Find one element in AclResource collection by unique key
     * @param Collection $collection
     * @param string $resource
     * @param string $privilege
     * @return AclResource|null
     */
    private function filterOne(Collection $collection, string $resource, string $privilege): ?AclResource
    {
        $result = $this->filterCollection($collection, $resource, $privilege);
        if ($result->count() === 0) {
            return null;
        }
        return $result->first();
    }

    /**
     * Generates resources map
     * @param object|null $entity
     * @param Form|null $form
     * @return Response|ViewModel|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editAction(object $entity = null, Form $form = null)
    {
        if (!$this->getRequest()->isPost()) {
            $url = $this->getURL('list');
            return $this->redirect()->toUrl($url);
        }
        $postParams = $this->params()->fromPost();

        // save all for developer and remove not existing
        /** @var Collection<AclResource> $savedResources */
        $savedResources = $this->getSavedResourcesByType();

        // ========== create new resources ==========
        foreach ($postParams as $resource => $privileges) {
            // create new resources
            foreach ($privileges as $privilege => $roleIds) {
                $AclResource = $this->filterOne($savedResources, $resource, $privilege);
                if ($AclResource === null) {
                    $new = $this->createResource($resource, $privilege);
                    $savedResources->add($new);
                }
            }
        }
        // ========== remove not existing resources ==========
        $toRemove = new ArrayCollection();
        /** @var AclResource $AclResource */
        foreach ($savedResources as $AclResource) {
            if (!isset($postParams[$AclResource->getResource()][$AclResource->getPrivilege()])) {
                $toRemove->add($AclResource);
            }
        }
        foreach ($toRemove as $element) {
            $savedResources->removeElement($element);
            $this->getEntityManager()->remove($element);
        }
        // ========== save changes ==========

        /** @var AclResourceDao $dao */
        $dao = $this->getEntityManager()->getRepository(AclResource::class);
        $dao->doSave($savedResources->toArray());

        // save for roles
        $roles = $this->getRoleIdMap();

        foreach ($roles as $UserRole) {
            $roleResources = $UserRole->getResources();

            foreach ($postParams as $resource => $privileges) {
                foreach ($privileges as $privilege => $allowed) {
                    /** @var AclResource $AclResource */
                    $AclResource = $this->filterOne($savedResources, $resource, $privilege);
                    //  can't be NULL, see above
                    $element = $this->filterOne($roleResources, $resource, $privilege);
                    if ($UserRole->getName() !== $UserRole::USER_ROLE_DEVELOPER) {
                        if ($allowed[$UserRole->getId()] === '1') {
                            if ($element === null) {
                                $roleResources->add($AclResource);
                            }
                        } elseif ($element !== null) {
                            $roleResources->removeElement($element);
                        }
                    } elseif ($element === null) {
                        $roleResources->add($AclResource);
                    }
                }
            }
        }
        $dao->doSave($roles);
        $this->addMessage('The access list generation was successfully completed', FlashMessenger::NAMESPACE_SUCCESS);
        $url = $this->getURL('list');
        return $this->redirect()->toUrl($url);
    }

    public function getResourceType(): string
    {
        return AclResource::RESOURCE_TYPE_MVC;
    }
}
