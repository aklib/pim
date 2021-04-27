<?php
    /**
     * Class EmptyAdapter
     * @package Application\View\Manager
     *
     * since: 30.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\View\Manager;

    use Acl\Service\AclService;
    use Application\Form\ConfirmForm;
    use Application\Form\EntityEdit;
    use Application\ServiceManager\AbstractAwareContainer;
    use Application\ServiceManager\Interfaces\AclAware;
    use Application\View\Manager\Interfaces\ViewAdapterInterface;

    class EmptyAdapter extends AbstractAwareContainer implements ViewAdapterInterface
    {

        public function getEntityName(): string
        {
            return '';
        }

        public function getContentSpecifications(): array
        {
            $content = [
                //'title' => 'User Management'
            ];
            if ($this->getActionName() === 'list') {
                $content['data'] = 'default/dataTable';
            } else {
                $content['data'] = 'default/dataForm';
            }
            return $content;
        }

        public function getLayoutSpecifications(): array
        {
            $layout = [
                //'title' => 'User Management'
            ];
            if ($this->getActionName() === 'list') {
                $layout['toolbar'] = 'toolbar/default-list';
            } else {
                $layout['toolbar'] = 'toolbar/default-edit';
            }
            return $layout;
        }

        public function getColumnsSpecifications(): array
        {
            return [];
        }

        public function getColumnFilterSpecifications(string $columnName): array
        {
            return [];
        }

        public function getActionsSpecifications(): array
        {
            $actions = [];

                $actions[] = [
                    'icon'  => 'la la-edit',
                    'route' => [
                        'name'   => 'default/admin',
                        'params' => [
                            'controller' => $this->getControllerAlias(),
                            'action'     => 'edit',
                        ],
                    ],
                    'attr'  => [
                        'title' => 'Edit',
                    ]
                ];
            return $actions;
        }


        public function getFormName(): string
        {
            if ($this->getActionName() === 'delete') {
                return ConfirmForm::class;
            }
            return EntityEdit::class;
        }

        public function isColumnVisible(string $columnName): bool
        {
            switch (strtolower($columnName)) {
                case 'password':
                case 'passwordresettoken':
                case 'passwordresettokencreationdate':
                case 'attributevalues':
                case 'configs':
                    return false;
            }
            if (!$this->getServiceManager()->has('authentication')) {
                return true;
            }
            return true;
            //return $this->getAcl()->isAllowedColumn(null, $this->getControllerName(), $columnName);
        }
    }