<?php
    /**
     * Class AbstractAttributeDao
     * @package Attribute\Repository
     *
     * since: 19.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Repository;


    use Application\Repository\AbstractDoctrineDao;
    use Application\Repository\AbstractModelDao;
    use Application\ServiceManager\Interfaces\Constant;
    use Application\Utils\DoctrineUtils;
    use Attribute\Entity\Attribute;
    use Attribute\Entity\AttributeValue;
    use Doctrine\ORM\QueryBuilder;
    use Exception;

    abstract class AbstractAttributeDao extends AbstractModelDao
    {
        private array $attributes = [];
        public int $joinCount = 0;

        /**
         * Override to handle attributes for filter and sort
         * @param $rawName
         * @param array $fields
         * @return mixed|null
         */
        protected function getFieldName($rawName, array $fields)
        {
            try {
                if ($this->getAttribute($rawName) instanceof Attribute) {
                    return parent::getFieldName($rawName, array_keys($this->attributes));
                }
            } catch (Exception $e) {
            }
            return parent::getFieldName($rawName, $fields);

        }

        /**
         * Handle filter by attribute
         * @param QueryBuilder $qb
         * @param $value
         * @param string $fieldName
         * @param bool $isAssociation
         * @return bool
         * @throws Exception
         */
        public function preCreateFilter(QueryBuilder $qb, $value, string $fieldName, bool $isAssociation): bool
        {
            if (!$isAssociation) {
                return true;
            }
            $attribute = $this->getAttribute($fieldName);
            if ($attribute === null) {
                return true;
            }
            if (empty($value)) {
                return false;
            }

            $field = $attribute->getType()->getField();
            $fieldAlias = $this->addAttributeValueJoin($qb, $attribute, $field);
            $applicationField = $this->getAssociationApplicationField($attribute);

            switch ($attribute->getType()->getElement()) {
                case 'select':
                case 'fieldset':
                    if ($applicationField !== 'title') {
                        DoctrineUtils::addFilter($qb, "=$value", $applicationField, $fieldAlias);
                    } else {
                        // creatives hook
                        DoctrineUtils::addFilter($qb, $value, $applicationField, $fieldAlias);
                    }
                    break;
                default:
                    DoctrineUtils::addFilter($qb, $value, $applicationField, $fieldAlias);

            }
            //dump($qb->getDQL());
            return false;
        }

        /**
         * Gets handled field
         * @param Attribute $attribute
         * @param bool $sort
         * @return string
         */
        private function getAssociationApplicationField(Attribute $attribute,  bool $sort = false): string
        {
            switch ($attribute->getType()->getField()) {
                case 'valueCountries':
                    return $sort ? 'code' : 'id';
                case 'valueCreatives':
                    return 'title';
            }
            return 'val';
        }

        /**
         * Creates join attributeValues and field like valueString, valueFloat... etc.
         * @param QueryBuilder $qb
         * @param Attribute $attribute
         * @param string $field
         * @param bool $sort
         * @return string
         */
        private function addAttributeValueJoin(QueryBuilder $qb, Attribute $attribute, string $field, bool $sort = false): string
        {
            $alias = $this->getAlias();
            $this->joinCount = $count = $this->joinCount + 1;

            $attributeValuesAlias = "attributeValues$count";

            // join attributeValues
            if ($sort) {
                $qb->leftJoin("$alias.attributeValues", $attributeValuesAlias);
            } else {
                $qb->innerJoin("$alias.attributeValues", $attributeValuesAlias);
            }
            // field eg. valueCreatives, valueString
            $fieldAlias = "$field$count";
            // join eg. attributeValues.valueStrings
            if ($sort) {
                $qb->leftJoin("$attributeValuesAlias.$field", $fieldAlias);
            } else {
                $qb->innerJoin("$attributeValuesAlias.$field", $fieldAlias);
            }

            if ($attribute->getType()->getType() === 'select') {
                return $fieldAlias;
            }
            switch ($attribute->getType()->getElement()) {
                case 'select':
                case 'fieldset':
                    $associationTargetClass = $this->getEntityManager()->getClassMetadata(AttributeValue::class)->getAssociationTargetClass($field);
                    /** @var AbstractDoctrineDao $dao */
                    $targetClass = $this->getEntityManager()->getClassMetadata($associationTargetClass)->getAssociationTargetClass('val');
                    /** @var AbstractDoctrineDao $dao */
                    $dao = $this->getEntityManager()->getRepository($targetClass);
                    $targetAlias = $dao->getAlias();
                    $targetAlias .= $count;
                    $qb->innerJoin("$fieldAlias.val", $targetAlias);
                    $fieldAlias = $targetAlias;
                    break;
                default:
            }
            return $fieldAlias;
        }


        /**
         * Handle sort by attribute
         * @param QueryBuilder $qb
         * @param $param
         * @param $isAssociation
         * @return bool
         * @throws Exception
         */
        public function preCreateSort(QueryBuilder $qb, $param, $isAssociation): bool
        {
            if (!$isAssociation) {
                return parent::preCreateSort($qb, $param, $isAssociation);
            }

            $order = explode(URL_VALUE_SEPARATOR, $param);
            $fieldName = $order[0];


            $attribute = $this->getAttribute($fieldName);
            if ($attribute === null) {
                return parent::preCreateSort($qb, $param, $isAssociation);
            }
            $field = $attribute->getType()->getField();
            $fieldAlias = $this->addAttributeValueJoin($qb, $attribute, $field, true);
            if ($attribute->getType()->getType() === 'select') {
                return false;
            }
            switch ($attribute->getType()->getElement()) {
                case 'select':
                    $order[0] = 'name';
                    DoctrineUtils::addSort($qb, implode(URL_VALUE_SEPARATOR, $order), $fieldAlias);
                    break;
                case 'fieldset':
                    break;
                default:
                    $order[0] = 'val';
                    DoctrineUtils::addSort($qb, implode(URL_VALUE_SEPARATOR, $order), $fieldAlias);
            }
            //dump($qb->getDQL());
            return false;
        }

        /**
         * Map all attributes by name
         * @param string $name
         * @return Attribute|null
         * @throws Exception
         */
        protected function getAttribute(string $name): ?Attribute
        {
            if (empty($this->attributes)) {
                /** @var Attribute $attribute */
                foreach ($this->getNamedQueryResult(Constant::NAMED_QUERY_ATTRIBUTES) as $attribute) {
                    $this->attributes[$attribute->getName()] = $attribute;
                }
            }
            return $this->attributes[$name] ?? null;
        }
    }