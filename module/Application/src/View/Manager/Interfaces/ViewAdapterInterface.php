<?php
    /**
     * Class ViewContentInterface
     * @package Application\View\Manager\Interfaces
     * since: 29.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\View\Manager\Interfaces;


    interface ViewAdapterInterface extends EntityAwareInterface
    {
        /**
         * Content configuration like header, subheader etc.
         * @return array
         */
        public function getContentSpecifications(): array;

        /**
         * Layout configuration like header, subheader etc.
         * @return array
         */
        public function getLayoutSpecifications(): array;

        /**
         * DataTable columns
         * @return array
         */
        public function getColumnsSpecifications(): array;

        public function isColumnVisible(string $columnName): bool;

        /**
         * DataTable columns
         * @param string $columnName
         * @return array
         */
        public function getColumnFilterSpecifications(string $columnName): array;

        /**
         * DataTable row actions/buttons
         * @return array
         */
        public function getActionsSpecifications(): array;

        /**
         * URL GET parameters
         * @return array
         */
        public function getUrlParams(): array;

        /**
         * Form class for edit|create ... actions
         * @return string
         */
        public function getFormName(): string;

        /**
         * The entity full class name
         * @return string
         */
        public function getEntityName(): string;
    }