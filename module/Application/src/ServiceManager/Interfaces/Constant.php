<?php
    /**
     * Class Constant
     * @package Application\ServiceManager\Interfaces * since: 13.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\ServiceManager\Interfaces;


    interface Constant
    {
        public const NAMED_QUERY_DROPDOWN_CHOICE = 'dropdownChoice';
        public const NAMED_QUERY_ATTRIBUTE_COLUMNS = 'attributeColumns';
        public const NAMED_QUERY_ATTRIBUTES = 'attributes';

        public const HTML_ATTRIBUTE_DATA_TAB = 'data-attribute-tab';
        public const HTML_ATTRIBUTE_DATA_ID = 'data-attribute-id';
        public const HTML_ATTRIBUTE_GROUP = 'data-group';
        public const INACTIVE = 2;
        public const ACTIVE = 1;
    }