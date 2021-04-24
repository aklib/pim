<?php
    /**
     * Class AttributeValueCountryDao
     * @package Attribute\Repository
     *
     * since: 12.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Repository;

    class AttributeValueCountryDao extends AbstractAttributeDao
    {
        protected function getAlias(): string
        {
            return 'valueCountries';
        }
    }