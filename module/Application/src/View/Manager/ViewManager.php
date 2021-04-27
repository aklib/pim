<?php

namespace Application\View\Manager;

use Application\Form\Factory\FormElementFactory;
use Exception;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\View\Model\ViewModel;
use User\Entity\UserConfig;

/**
 * Class ViewManager
 * since: 14.07.2020
 * author: alexej@kisselev.de
 */
class ViewManager extends AbstractViewManager
{
    private ?ViewModel $viewModel = null;

    public function getLayout(): array
    {
        if (empty($this->layout)) {
            $layout = array_replace_recursive(parent::getLayout(), $this->getAdapter()->getLayoutSpecifications());
            $this->setFromArray(['layout' => $layout]);
        }
        return parent::getLayout();
    }

    public function getContent(): array
    {
        if (empty($this->content)) {
            $content = array_replace_recursive(parent::getContent(), $this->getAdapter()->getContentSpecifications());
            $this->setFromArray(['content' => $content]);
        }
        return parent::getContent();
    }

    public function getFormName(): string
    {
        return $this->getAdapter()->getFormName();
    }

    /**
     * Table columns
     * @return array
     */
    public function getColumns(): array
    {
        $entityName = $this->getAdapter()->getEntityName();
        if (empty($entityName)) {
            $entityName = $this->getEntityName();
        }
        if (empty($entityName)) {
            // old engine - .ini file
            return parent::getColumns();
        }

        if (isset($this->columns[$entityName])) {
            return $this->columns[$entityName];
        }

        $classMetadata = $this->getEntityManager()->getClassMetadata($entityName);

        $mappings = array_replace_recursive($classMetadata->fieldMappings, $classMetadata->associationMappings);
        $columns = [];
        $count = 0;
        $controllerName = $this->getControllerName();
        foreach ($mappings as $fieldName => $mapping) {
            if (!$this->getAdapter()->isColumnVisible($fieldName)) {
                continue;
            }

            if ($this->hasAcl() && !$this->getAcl()->isAllowedColumn(null, $controllerName, $fieldName)) {
                continue;
            }

            $column = [
                'name'      => $fieldName,
                'label'     => $fieldName,
                'sortOrder' => $count += 10,
                'hidden'    => false,
                'type'      => $mapping['type']
            ];
            if (!empty($mapping['column']) && is_array(($mapping['column']))) {
                foreach ($mapping['column'] as $key => $value) {
                    if(!empty($value)){
                        $column[$key] = $value;
                    }
                }
            }
            $columns[$fieldName] = $column;
        }
        $columns = array_replace_recursive($columns, $this->getAdapter()->getColumnsSpecifications());
        foreach ($columns as $name => $column) {
            if (!empty($name) && !isset($column['hidden'])) {
                $columns[$name]['hidden'] = false;
            }
        }
        /** @var UserConfig $currentConfig */
        $currentConfig = $this->getUserConfig();
        //load from config if exists
        if ($currentConfig !== null) {
            //dump($configColumns);
            foreach ($currentConfig->getColumns() as $name => $configColumn) {
                if (!isset($columns[$name])) {
                    // renamed/removed/acl-restricted
                    continue;
                }
                foreach ($configColumn as $key => $value) {
                    $columns[$name][$key] = $value;
                }
            }
        }

        uasort($columns, static function ($a, $b) {
            return $a['sortOrder'] > $b['sortOrder'];
        });
        $this->columns[$entityName] = $columns;
        return $columns;
    }

    /**
     * Table row actions
     * @return array
     */
    public function getActions(): array
    {
        if ($this->getAdapter()->getEntityName() === '') {
            // old engine - .ini file
            return parent::getActions();
        }
        if (isset($this->actions[$this->getAdapter()->getEntityName()])) {
            return $this->actions[$this->getAdapter()->getEntityName()];
        }
        $actions = $this->getAdapter()->getActionsSpecifications();
        $this->actions[$this->getAdapter()->getEntityName()] = $actions;
        return $actions;
    }

    /**
     * @param string $colName
     * @return Element
     */
    public function getColumnFilter(string $colName): ?Element
    {
        $column = $this->getColumn($colName);
        if (!is_array($column)) {
            return null;
        }
        /** @var FormElementFactory $formElementFactory */
        $formElementFactory = $this->getServiceManager()->get(FormElementFactory::class);

        $element = $formElementFactory->createColumnFilter($colName, $this->getEntityName());
        if ($element === null) {
            return null;
        }
        $urlParams = $this->getUrlParams();
        //var_dump($urlParams);die;
        if (!empty($urlParams[$colName])) {
            $element->setAttribute('value', $urlParams[$colName]);
        }
        return $element;
    }

    /**
     * Gets url query for urlx() view helper to remove all filters. See default/dataTable.phtml
     * @return array
     */
    public function getClearFilterQuery(): array
    {
        $query = [];
        foreach ($this->getColumns() as $column) {
            $query[$column['name']] = null;
        }
        return $query;
    }

    /**
     * Creates a ViewModel, sets configured variables and templates
     * @return ViewModel
     * @throws Exception
     */
    public function createViewModel(): ViewModel
    {
        if ($this->viewModel instanceof ViewModel) {
            return $this->viewModel;
        }
        //create data view (renders result as table, thumbnail, portlet etc.)
        //available as echo $this->data in a list.phtml

        $layoutConfig = $this->getLayout();
        /** @var ViewModel $layoutViewModel */
        $layoutViewModel = $this->getServiceManager()->get('Application')->getMvcEvent()->getViewModel();
        // init layout
        if (is_array($layoutConfig)) {
            /*
             * Allowed keys
             * title
             * subtitle
             * toolbar
             * any - not handled
             */

            foreach ($layoutConfig as $key => $value) {
                $layoutViewModel->setVariable($key, $value);
            }
        }

        // create view model
        $viewModel = new ViewModel();
        $contentConfig = $this->getContent();
        if (is_array($contentConfig)) {
            /*
             * Allowed keys
             * data - template
             * top - template
             * footer - template
             * title - variable
             * subtitle - variable
             * any - not handled
             */
            foreach ($contentConfig as $key => $value) {
                switch ($key) {
                    case 'data':
                    case 'top':

                        $dataModel = new ViewModel();
                        $dataModel->setTemplate($value);
                        $viewModel->addChild($dataModel, $key);
                        break;
                    default:
                        $viewModel->setVariable($key, $value);
                }
            }

            // required
            $dataModels = $viewModel->getChildrenByCaptureTo('data');
            if (empty($dataModels)) {
                $dataModel = new ViewModel();
                if ($this->getActionName() === 'list') {
                    $dataModel->setTemplate('default/dataTable');
                } else {
                    $dataModel->setTemplate('default/dataForm');
                }
                $viewModel->addChild($dataModel, 'data');
            }
            if ($this->getUserConfig() instanceof UserConfig) {
                $asideFilters = [];
                foreach ($this->getColumns() as $name => $column) {
                    if ($column['hidden'] === true) {
                        $asideFilters[$name] = $column;
                    }
                }
                if (!empty($asideFilters)) {
                    $asideModel = $this->createAsideFilterModel($asideFilters);
                    if ($asideModel !== null) {
                        $layoutViewModel->addChild($asideModel, 'aside');
                    }
                }
            }

            $viewModel->setTerminal($this->getRequest()->isXmlHttpRequest());
        }
        return $this->viewModel = $viewModel;
    }

    /**
     * @param array $columns
     * @return ViewModel|null
     * @throws Exception
     */
    private function createAsideFilterModel(array $columns): ?ViewModel
    {
        if (empty($columns)) {
            return null;
        }
        $asideModel = new ViewModel();
        $asideModel->setTemplate('layout/aside');

        $asideContentModel = new ViewModel();
        $asideContentModel->setTemplate('default/asideFilter');
        $asideModel->addChild($asideContentModel, 'content');
        $form = new Form('filters');
        foreach ($columns as $name => $column) {
            $element = $this->getColumnFilter($name);
            if ($element === null) {
                continue;
            }
            $form->add($element);
        }
        $asideContentModel->setVariable('form', $form);
        return $asideModel;
    }
}