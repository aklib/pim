<?php

/**
 * AbstractModuleController.php
 *
 * @since 01.04.2018
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Controller;

use Acl\Entity\AclResource;
use Acl\Repository\AclResourceDao;
use Acl\Service\AclService;
use Application\Exception\ApplicationShownException;
use Application\Form\AbstractAwareForm;
use Application\Form\ConfirmForm;
use Application\Form\ListForm;
use Application\ModuleOptions as ApplicationModuleOptions;
use Application\Repository\AbstractAwareDao;
use Application\Repository\AbstractDoctrineDao;
use Application\ServiceManager\Interfaces\AuthenticationAware;
use Application\ServiceManager\Interfaces\Constant;
use Application\ServiceManager\Interfaces\EntityManagerAware;
use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Application\ServiceManager\Interfaces\ViewModelAware;
use Application\Utils\ClassUtils;
use Application\View\Helper\History;
use Application\View\Manager\ViewManager;
use Application\View\Model\Tab\TabFactory;
use Application\View\Model\Tab\TabItem;
use Attribute\Entity\AttributeTab;
use Attribute\Hydrator\AttributeEntityHydrator;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\UnitOfWork;
use Exception;
use FlorianWolters\Component\Core\StringUtils;
use InvalidArgumentException;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Helper\PaginationControl;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use User\Controller\AuthController;
use User\Entity\User;
use User\Entity\UserConfig;
use User\Repository\UserConfigDao;

/**
 *
 * @method Request getRequest()
 * @method string translate($string)
 * @method mixed decorate($item, $method)
 * @method AclService acl()
 * @method History history()
 * @method User|null identity()
 * @method string urlx(array $params = null)
 * @method bool isAllowedColumn()
 * @method bool isAjax()
 * @method FlashMessenger flashMessenger()
 */
abstract class AbstractModuleController extends AbstractActionController implements ServiceManagerAware, EntityManagerAware, AuthenticationAware
{
    private ContainerInterface $sm;
    private EntityManager $em;
    private User $currentUser;

    /**
     * Triggered on send response. Save a valid request into history()
     * @param MvcEvent $e
     * @noinspection PhpUnused
     */
    public function onFinish(MvcEvent $e): void
    {
        if ($e->isError()) {
            return;
        }
        if (get_class($this) === AuthController::class) {
            return;
        }
        if (!$this->getRequest()->isPost() && !$this->getRequest()->isXmlHttpRequest()) {
            $this->history()->setControllerName($this->getControllerName())
                ->setActionName($this->getActionName())
                ->setModuleName($this->getModuleName());
            $this->history()->save();

        }
    }

    /**
     * Set the service manager.
     *
     * @param ContainerInterface $sm
     * @return void
     */

    public function setServiceManager(ContainerInterface $sm): void
    {
        $this->sm = $sm;
    }

    /**
     * Get the service manager.
     *
     * @return ContainerInterface
     */
    public function getServiceManager(): ContainerInterface
    {
        return $this->sm;
    }

    /**
     * Gets Doctrine\ORM\EntityManager
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    /**
     * Sets Doctrine\ORM\EntityManager
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }

    /**
     *
     * @param string $message
     * @param string $namespace info/error/success/default
     * @return void
     */
    protected function addMessage(string $message, string $namespace): void
    {
        $this->flashMessenger()->addMessage($message, $namespace);
    }

    /**
     * AuthenticationAware interface implementation
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->currentUser;
    }

    /**
     * AuthenticationAware interface implementation
     * @param User $currentUser
     */
    public function setCurrentUser(User $currentUser): void
    {
        $this->currentUser = $currentUser;
    }

    /*
* ================================================
* SHARED ACTIONS for child Controllers
* ================================================
*/

    /**
     * Shared action for all listings in Project (if is't overridden) like eg. product list
     * Process:
     * 1. Find model class from view config ini file. Class must implements the ViewAwareInterface
     * 2. Find method of this class (by default getList) and call it.
     * 3.
     *
     * @param ViewModel|null $viewModel
     * @param array $options // keys: configurable
     * @return ViewModel
     */
    public function listAction(ViewModel $viewModel = null, array $options = []): ViewModel
    {
        /*
         * FORM for filters
         */
        $form = $this->getServiceManager()->get(ListForm::class);
        $this->layout()->setVariable('form', $form);
        /*
         * VIEW MANAGER for entity, columns, view model
         */
        /** @var ViewManager $manager */
        $manager = $this->getServiceManager()->get(ViewManager::class);
        $manager->setUserConfig($this->getUserConfig());
        // merge url parameters from config and url query/post
        $urlParams = $manager->getUrlParams();
        $repository = $manager->getRepository();
        //get result
        if (null === $viewModel) {
            $viewModel = $manager->createViewModel();
        }
        if ($repository === null) {
            return $viewModel;
        }

        $modelFn = 'getList';
        $result = $repository->{$modelFn}($manager->getUrlParams(), $options);
        $dataModel = $viewModel->getChildrenByCaptureTo('data')[0];
        /*
         * Configure paginator
         * set partial template, must be in templates/pagination/other.phtml
         * set sliding type:
         * All          Returns every page. This is useful for dropdown menu pagination controls with relatively few pages. In these cases, you want all pages available to the user at once.
         * Elastic 	A Google-like scrolling style that expands and contracts as a user scrolls through the pages.
         * Jumping 	As users scroll through, the page number advances to the end of a given range, then starts again at the beginning of the new range.
         * Sliding 	(Default). A Yahoo!-like scrolling style that positions the current page number in the center of the page range, or as close as possible. This is the default style.
         */
        if ($result instanceof Paginator) {
            Paginator::setDefaultScrollingStyle('Elastic');
            PaginationControl::setDefaultViewPartial(
                'default/pager.phtml'
            );
            $limits = isset($urlParams['limits']) ? array_map('intval', $urlParams['limits']) : [20, 50, 100, 200];
            $viewModel->setVariable('limits', $limits);

            $limit = (int)($urlParams['limit'] ?? 20);
            if (empty($limit) || !in_array($limit, $limits, true)) {
                $limit = $limits[0];
            }

            $result->setCurrentPageNumber(empty($urlParams['page']) ? 1 : $urlParams['page'])
                ->setPageRange(5)
                ->setItemCountPerPage($limit);

            /*
             * =================== INIT VIEW ==========================
             */

            $viewModel->setVariable('paginator', $result);
            $dataModel->setVariable('data', $result->getCurrentItems());
            if ($this->getServiceManager()->has('acl')) {
                if (!isset($options['configurable']) || $options['configurable'] !== false) {
                    // toolbar
                    /** @var AclResourceDao $dao */
                    $dao = $this->getEntityManager()->getRepository(AclResource::class);
                    $criteria = [
                        'type'      => 'mvc',
                        'resource'  => (string)get_class($this),// (string) - escape backslashes
                        'privilege' => $this->getActionName()
                    ];
                    // init view configuration button
                    $this->layout()->setVariable('aclResource', $dao->findOneBy($criteria));
                }
            }

        } elseif (!empty($dataModel)) {
            $dataModel->setVariable('data', $result);
        }
        return $viewModel;
    }

    /**
     * @param object|null $entity
     * @param Form|null $form
     * @return Response|ViewModel|null
     * @throws Exception
     */
    protected function editAction(object $entity = null, Form $form = null)
    {
        /** @var ViewManager $manager */
        $manager = $this->getServiceManager()->get(ViewManager::class);
        /** @var AbstractAwareDao $repository */
        $repository = $manager->getRepository();
        $viewModel = $manager->createViewModel();
        $id = (int)$this->params()->fromRoute('id');
        try {
            //============= INIT ENTITY to edit | create =============
            if ($entity === null) {
                // edit existing entity

                if (empty($id)) {
                    return $viewModel;
                }
                if ($repository instanceof ViewModelAware) {
                    $entity = $repository->getDetails($id);
                } else {
                    $entity = $repository->find($id);
                }
                if ($entity === null) {
                    $message = $this->translate('The entry #%d was not found in the database');
                    throw new ApplicationShownException(sprintf($message, $id));
                }
            }
            $isNew = $this->getEntityManager()->getUnitOfWork()->getEntityState($entity) === UnitOfWork::STATE_NEW;

            //============= INIT FORM =============
            $viewModel->setVariable('entity', $entity);
            if ($form === null) {
                // get form from config (controller_name.ini[edit|create])
                $formClass = $manager->getFormName();
                if (empty($formClass)) {
                    return $viewModel;
                }
                /** @var Form $form */
                if ($this->getServiceManager()->has($formClass)) {
                    $form = $this->getServiceManager()->get($formClass);
                    if ($form instanceof AbstractAwareForm) {
                        $form->createForm($entity);
                    }
                }
                if ($form === null) {
                    throw new InvalidArgumentException('No configured form found! Set form class in the config file or set as parameter');
                }
            }

            $viewModel->setVariable('form', $form);
            /** @var ViewModel $dataModel */
            $dataModel = $viewModel->getChildrenByCaptureTo('data')[0];

            $dataModel->setVariable('form', $form);
            $this->createTabs($form, $viewModel);
            //============= UPDATE | CREATE ENTITY =============

            if ($this->getRequest()->isPost()) {
                $form->setData($this->getRequest()->getPost());
                if ($form->isValid()) {
                    //@TODO find a solution
                    $data = $this->postValidateFormData(array_replace_recursive($this->getRequest()->getPost()->toArray(), $form->getData()), $entity);
                    $hydrator = $this->getServiceManager()->get(DoctrineObject::class);
                    $hydrator->hydrate($data, $entity);
                    $this->afterHydrationFormData($form->getData(), $entity);
                    if ($repository instanceof AbstractAwareDao) {
                        $repository->doSave($entity);
                    } else {
                        $this->getEntityManager()->persist($entity);
                        $this->getEntityManager()->flush($entity);
                    }
                    $submit = $this->params()->fromPost('submit');
                    if ($submit === 'back') {
                        $url = $this->getURL('list', $this->getControllerName(), null);
                    } else {
                        $identifierValues = $this->getEntityManager()->getClassMetadata(get_class($entity))->getIdentifierValues($entity);
                        $id = array_shift($identifierValues);
                        $url = $this->getURL('edit', $this->getControllerName(), $id);
                    }
                    //die('AbstractModuleController');
                    $action = $isNew ? 'created' : 'updated';
                    $object = $this->translate(ClassUtils::getShortName($entity));
                    $idString = $isNew ? '' : " #$id";
                    $this->addMessage(sprintf("The %s%s has been successfully %s", $object, $idString, $action),
                        FlashMessenger::NAMESPACE_SUCCESS);
                    $this->postSaveEntity($entity);
                    if ($this->isAjax()) {
                        return $viewModel->setTemplate('js/reload');
                    }
                    //return $viewModel;
                    return $this->redirect()->toUrl($url);
                }
                // show form fields errors
                return $viewModel;
            }
            return $viewModel;
        } catch (ApplicationShownException $e) {
            $this->addMessage($e->getMessage(), FlashMessenger::NAMESPACE_ERROR);
            if ($this->isAjax()) {
                return $viewModel->setTemplate('js/reload');
            }
            return $this->redirect()->toUrl($this->urlx(['action' => 'list', 'id' => null]));
        } catch (Exception $e) {
            /** @var ApplicationModuleOptions $applicationOptions */
            $applicationOptions = $this->getServiceManager()->get(ApplicationModuleOptions::class);
            if (!$applicationOptions->isDisplayExceptions()) {
                $this->addMessage('#generic: request error message', FlashMessenger::NAMESPACE_ERROR);
                if ($this->isAjax()) {
                    return $viewModel->setTemplate('js/reload');
                }
                return $this->redirect()->toUrl($this->urlx());
            }
            // for development mode
            throw $e;
        }
        /** @noinspection PhpUnreachableStatementInspection */
        return $viewModel;
    }

    /**
     * Creates tab form
     * @param Form $form
     * @param ViewModel $viewModel
     */
    protected function createTabs(Form $form, ViewModel $viewModel): void
    {
        $hasTabs = $form->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB);
        if ($hasTabs !== true) {
            return;
        }
        $elementsByTab = [];
        $tabsMap = [];
        /** @var Element $element */
        foreach ($form->getElements() as $element) {
            if ($element->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB) === null) {
                // add to general
                $element->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, 'general');
            }
            $key = strtolower($element->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB));
            $elementsByTab[$key][] = $element;
            $tabsMap[$key] = $element->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB);
        }
        foreach ($form->getFieldsets() as $fieldset) {
            if ($fieldset->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB) === null) {
                // add to general
                $fieldset->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, 'general');
            }
            $key = strtolower($fieldset->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB));
            $elementsByTab[$key][] = $fieldset;
            $tabsMap[$key] = $fieldset->getAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB);
        }
        if (count($elementsByTab) === 1) {
            //don't show a single tab
            return;
        }

        /** @var AbstractDoctrineDao $dao */
        $dao = $this->getEntityManager()->getRepository(AttributeTab::class);
        $tabs = $dao->findByLabel(array_keys($tabsMap));
        /** @var AttributeTab $tab */
        foreach ($tabs as $tab) {
            $tabsMap[strtolower($tab->getLabel())] = $tab;
        }
        foreach ($elementsByTab as $key => $elements) {
            /** @var AttributeTab $attributeTab */
            $attributeTab = $tabsMap[$key] instanceof AttributeTab ? $tabsMap[$key] : null;
            $label = $attributeTab === null ? $tabsMap[$key] : $attributeTab->getLabel();
            $fieldset = new Fieldset($label);
            /** @var Element $element */
            foreach ($elements as $element) {
                $fieldset->add($element);
            }
            try {
                $tab = TabFactory::addTab([
                    'title'     => $label,
                    'useAcl'    => false,
                    'content'   => $fieldset,
                    'sortOrder' => $attributeTab !== null ? $attributeTab->getSortOrder() : 100
                ]);
                $tab->addClass('card-body');
                if ($attributeTab !== null) {
                    $tab->setAttributeTab($attributeTab);
                }
                $this->postCreateTab($tab, $attributeTab);
            } catch (Exception $e) {
            }
        }
        $dataViewModel = $viewModel->getChildrenByCaptureTo('data')[0];
        TabFactory::setOrientation(TabFactory::ORIENTATION_HORIZONTAL);
        //TabFactory::addContainerClass('nav-pills');
        $viewModel->setTemplate('default/tabs');
        $viewModel->setVariable('form', null);
        $this->layout()->setVariable('form', $form);
        TabFactory::sort();
        TabFactory::setViewModel($dataViewModel);
    }

    /**
     * @return Response|ViewModel|null
     * @throws ORMException
     * @noinspection PhpUnused
     */
    public function configureAction()
    {
        $resourceId = (int)$this->params()->fromRoute('id');
        /** @var AclResourceDao $aclDao */
        $aclDao = $this->getEntityManager()->getRepository(AclResource::class);

        /** @var AclResource $aclResource */
        $aclResource = $aclDao->find($resourceId);
        if ($aclResource === null) {
            $this->addMessage('The view is not configurable. Unknown resource', FlashMessenger::NAMESPACE_WARNING);
            return $this->redirect()->toUrl($this->getURL());
        }
        // init view
        /** @var ViewManager $manager */
        $manager = $this->getServiceManager()->get(ViewManager::class);
        $viewModel = $manager->createViewModel();
        $viewModel->setTemplate('default/list');
        $dataModel = $viewModel->getChildrenByCaptureTo('data')[0];
        $dataModel->setTemplate('configuration/list');
        $currentConfig = $this->getUserConfig($aclResource->getResource(), $aclResource->getPrivilege());
        $manager->setUserConfig($currentConfig);
        $columns = $manager->getColumns(true);
        $dataModel->setVariable('columns', $columns);

        $dataModel->setVariable('currentConfig', $currentConfig);

        /** @var ListForm $form */
        $form = $this->getServiceManager()->get(ListForm::class);
        $form->setAttribute('method', 'post');
        $this->layout()->setVariable('form', $form);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $visibleColumns = [];
            $sortOrder = 1;
            foreach ($data as $name => $visible) {
                $columns[$name]['hidden'] = $visible === '0';
                $columns[$name]['sortOrder'] = $sortOrder++;
                if ($visible === '1') {
                    $visibleColumns[] = $name;
                }

                /*
                if (isset($columns[$name]) && $visible === '1') {
                    $visibleColumns[$name] = [
                        'name'      => $name,
                        'sortOrder' => $sortOrder++
                    ];
                }*/
            }
            if (empty($visibleColumns)) {
                $this->addMessage('No columns has been selected', FlashMessenger::NAMESPACE_WARNING);
                return $this->redirect()->toUrl($this->getURL());
            }

            if ($currentConfig === null) {
                $currentConfig = new UserConfig();
                $currentConfig->setCurrent(true);
                $currentConfig->setName('default');
                $currentConfig->setResource($this->getResourceUniqueKey($aclResource->getResource(), $aclResource->getPrivilege()));
                $this->getCurrentUser()->addUserConfig($currentConfig);
                $this->getEntityManager()->persist($currentConfig);
            }

            try {
                $currentConfig->setColumns(json_encode($columns, JSON_THROW_ON_ERROR));
                /** @var UserConfigDao $dao */
                $dao = $this->getEntityManager()->getRepository(UserConfig::class);
                $dao->doSave($currentConfig);
                $this->addMessage('The configuration has been successfully saved', FlashMessenger::NAMESPACE_SUCCESS);
            } catch (Exception $e) {
                $this->addMessage($e->getMessage(), FlashMessenger::NAMESPACE_ERROR);
            }

            if ($data['submit'] === 'back') {
                $url = $this->getURL('list', $this->getControllerName(), null);
            } else {
                $url = $this->getURL('configure', $this->getControllerName());
            }
            return $this->redirect()->toUrl($url);
        }
        return $viewModel;
    }

    protected function deleteAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        if ($this->getRequest()->isPost()) {
            $id = (int)$this->params()->fromRoute('id');
            /** @var ViewManager $manager */
            $manager = $this->getServiceManager()->get(ViewManager::class);
            /** @var AbstractAwareDao $repository */
            $repository = $manager->getRepository();
            $entityD = $repository->find($id);
            if ($entityD !== null) {
                /** @var AbstractDoctrineDao $dao */
                $dao = $this->getEntityManager()->getRepository(get_class($entityD));
                $dao->doDelete($entityD);
                $name = $this->translate(ClassUtils::getShortName($entityD));
                $this->addMessage(sprintf("The %s#%s has been successfully deleted", $name, $id), FlashMessenger::NAMESPACE_SUCCESS);
            } else {
                $this->addMessage(sprintf("The entry#%s not found in database", $id), FlashMessenger::NAMESPACE_ERROR);
            }

            if ($this->isAjax()) {
                $viewModel = new ViewModel();
                return $viewModel->setTemplate('js/reload');
            }
            $url = $this->getURL('list', $this->getControllerName());
            return $this->redirect()->toUrl($url);
        } else {
            /** @var ConfirmForm $confirmForm */
            $confirmForm = $this->getServiceManager()->get(ConfirmForm::class);
            $confirmForm->setAttribute('action', $this->urlx(['action' => 'delete']));
            $viewModel = $this->editAction(null, $confirmForm);
            $entityD = $viewModel->getVariable('entity');
            $name = $this->translate(ClassUtils::getShortName($entityD));

            $viewModel->setVariable('title', "Delete Confirm");
            $viewModel->setVariable('messages', "Do you really want to delete the $name?");
            $viewModel->setVariable('buttonOK', 'Yes, delete');
            $viewModel->setTemplate('modal/custom');
        }
        return $viewModel;
    }

//=========================== NO ACTION METHODS ===========================

    /**
     * Creates an unique key for current request resource like 'controllerName::actionName'. Similar to acl resource
     * @param string|null $controller
     * @param string|null $action
     * @return string
     */
    protected function getResourceUniqueKey(string $controller = null, string $action = null): string
    {
        if ($controller === null) {
            $controller = (string)strtolower(get_class($this));
        }
        if ($action === null) {
            $action = strtolower($this->getActionName());
        }
        return "$controller::$action";
    }

    /**
     * Gets current user config
     * @param string|null $controller
     * @param string|null $action
     * @return UserConfig|null
     */
    protected function getUserConfig(string $controller = null, string $action = null): ?UserConfig
    {
        if (!$this->getServiceManager()->has('authentication')) {
            return null;
        }
        $key = $this->getResourceUniqueKey($controller, $action);
        $userConfigs = $this->getCurrentUser()->getConfigs();
        /** @var UserConfig $userConfig */
        foreach ($userConfigs as $userConfig) {
            if ($userConfig->isCurrent() && $userConfig->getResource() === $key) {
                return $userConfig;
            }
        }
        return null;
    }

    /**
     * Gets a controller name like 'offer', 'user' etc.
     * @return string
     */
    protected function getControllerName(): string
    {
        return str_replace('controller', '', strtolower(StringUtils::substringAfterLast($this->params()->fromRoute('controller'), '\\')));
    }

    /**
     * Gets a action name like 'list', 'edit' etc.
     * @return string
     */
    protected function getActionName(): string
    {
        return $this->params()->fromRoute('action');
    }

    /**
     * Gets a module name like 'application', 'user' etc.
     * @return string
     */
    private function getModuleName(): string
    {
        return strtolower(ClassUtils::getNamespace($this));
    }

    /**
     * @param string|null $actionName
     * @param string|null $controllerName
     * @param int|null $id
     * @return string
     */
    protected function getURL(string $actionName = null, string $controllerName = null, int $id = null): string
    {

        if ($actionName === null && $controllerName === null && $id === null) {
            $last = $this->history()->getLastUrl();
            if (!empty($last)) {
                return $last;
            }
        }
        $url = $id === null ? $this->history()->getLastUrlFor($actionName, $controllerName) : null;
        if (empty($url)) {
            $param = [];
            if (!empty($controllerName)) {
                $param['controller'] = $controllerName;
            }
            if (!empty($actionName)) {
                $param['action'] = $actionName;
            }
            $param['id'] = $id;
            $url = $this->urlx($param);

            /* $url = $this->url()->fromRoute('default/' . $this->getModuleName(), [
                     'controller' => $controllerName ?? $this->getControllerName(),
                     'action'     => $actionName ?? 'list',
                     'id'         => $id ?? $this->params()->fromRoute('id')
                 ]
             );*/
        }
        return $url;
    }

    /**
     * @param array $data
     * @param object|null $entity
     * @return array
     * @noinspection PhpUnusedParameterInspection
     */
    protected function postValidateFormData(array $data, object $entity = null): array
    {
        return $data;
    }

    protected function afterHydrationFormData(array $data, object $object): void
    {

    }

    /**
     * @param object|null $entity
     */
    protected function postSaveEntity(?object $entity): void
    {
    }

    protected function postCreateTab(TabItem $tabItem, AttributeTab $attributeTab = null): void
    {

    }

}