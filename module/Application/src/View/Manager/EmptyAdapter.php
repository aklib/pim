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

    class EmptyAdapter extends AbstractAwareContainer implements ViewAdapterInterface, AclAware
    {

        private AclService $acl;

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
            if ($this->getAcl()->isAllowed(null, $this->getControllerName(), 'edit')) {
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
            }
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
            return $this->getAcl()->isAllowedColumn(null, $this->getControllerName(), $columnName);
        }


        /**
         * AclAware implementation
         * @return AclService
         */
        public function getAcl(): AclService
        {
            return $this->acl;
        }

        /**
         * AclAware implementation
         * @param AclService $acl
         */
        public function setAcl(AclService $acl): void
        {
            $this->acl = $acl;
        }

        public function hasAcl(): bool
        {
            return $this->getServiceManager()->has('acl');
        }
    }