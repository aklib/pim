<?php /** @noinspection TransitiveDependenciesUsageInspection */
/** @noinspection PhpUnused */

/**
 * Class AbstractViewContentManager
 * since: 17.07.2020
 * author: alexej@kisselev.de
 */

namespace Application\View\Manager;

use Acl\Service\AclService;
use Application\ModuleOptions;
use Application\ServiceManager\AbstractAwareContainer;
use Application\ServiceManager\Interfaces\AclAware;
use Application\ServiceManager\Interfaces\ViewModelAware;
use Laminas\Hydrator\ClassMethodsHydrator;
use RuntimeException;
    use User\Entity\UserConfig;

abstract class AbstractViewManager extends AbstractAwareContainer implements AclAware
{

    private AclService $acl;
    protected array $columns = [];
    protected array $actions = [];
    protected array $content = [];
    protected array $layout = [];
    protected string $form = '';
    protected ?UserConfig $userConfig = null;

        /**
         * @return array
         */
        public function getColumns(): array
        {
            return $this->columns;
        }

        /**
         * @param array $columns
         */
        protected function setColumns(array $columns): void
        {
            $this->columns = $columns;
        }

        /**
         * @return array
         */
        public function getActions(): array
        {
            return $this->actions;
        }

        /**
         * @param array $actions
         */
        protected function setActions(array $actions): void
        {
            $this->actions = $actions;
        }

        /**
         * @return array
         */
        public function getContent(): array
        {
            return $this->content;
        }

        /**
         * @param array $content
         */
        protected function setContent(array $content): void
        {
            $this->content = $content;
        }

        /**
         * @return array
         */
        public function getLayout(): array
        {
            return $this->layout;
        }

        /**
         * @param array $layout
         */
        protected function setLayout(array $layout): void
        {
            $this->layout = $layout;
        }

        /**
         * @return string
         */
        public function getFormName(): string
        {
            return $this->form;
        }

        /**
         * @param string $form
         */
        protected function setForm(string $form): void
        {
            $this->form = $form;
        }

    public function getEntityName(): string
    {
        if (parent::getEntityName() === '') {
            $this->setEntityName($this->getAdapter()->getEntityName());
        }
        return parent::getEntityName();
    }
        /**
         * Gets entity repository initialised implemented interfaces
         * @param string|null $entityName
         * @return ViewModelAware
         */
        public function getRepository(string $entityName = null): ?ViewModelAware
        {
            if ($entityName === null) {
                $entityName = $this->getEntityName();
            }
            if ($this->isEmptyAdapter($entityName)) {
                if (!$this->displayExceptions()) {
                    throw new RuntimeException(sprintf("Dear developer, implement finally the '%s' class extends 'Application\View\Manager\EmptyAdapter' and override '%s'::getEntityName()! Eg. getEntityName(){return User::class;}",
                        $this->getAdapterName(), $this->getAdapterName()));
                }
                $this->setFromArray([
                        'content' => [
                            'title' => 'Empty View'
                        ]
                    ]
                );
                return null;
            }
            $repository = $this->getEntityManager()->getRepository($entityName);
            if (!($repository instanceof ViewModelAware)) {
                throw new RuntimeException('View repository should be instance of ViewModelAware');
            }
            return $repository;
        }

        /**
         * @param string $colName
         * @return array|null
         */
        public function getColumn(string $colName): ?array
        {
            if (empty($colName)) {
                return null;
            }
            return $this->getColumns()[$colName] ?? null;
        }

        protected function isEmptyAdapter(string $entityName = null): bool
        {
            return $this->getEntityName() === '' && ($entityName === null || $entityName === '');
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
            return $this->getServiceManager()->has('acl');
        }

        /**
         * Gets user current config
         * @return UserConfig|null
         */
        public function getUserConfig(): ?UserConfig
        {
            return $this->userConfig;
        }

        /**
         * @param UserConfig|null $userConfig
         */
        public function setUserConfig(?UserConfig $userConfig): void
        {
            $this->userConfig = $userConfig;
        }
    }