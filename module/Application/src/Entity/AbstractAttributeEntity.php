<?php
    /**
     * Class AttributableEntity
     * @package Application\Entity
     *
     * since: 12.08.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Entity;


    use Attribute\Entity\AttributeValue;
    use Doctrine\Common\Collections\Collection;
    use FlorianWolters\Component\Core\StringUtils;

    abstract class AbstractAttributeEntity
    {
        /**
         * Find elements in AclResource collection by unique key
         * @param Collection $collection
         * @param string $name
         * @return Collection
         * @noinspection PhpInconsistentReturnPointsInspection
         */
        private function filterCollection(Collection $collection, string $name): ?Collection
        {
            if ($collection->isEmpty()) {
                return null;
            }
            return $collection->filter(static function (AttributeValue $element) use ($name) {
                // filter by unique key
                if (strcasecmp($element->getAttribute()->getName(), $name) === 0) {
                    return $element;
                }
            });
        }

        private function decamelize($string): string
        {
            return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
        }

        /**
         * @param $getter
         * @param $args
         * @return int|float|string|Collection
         * @noinspection PhpUnusedParameterInspection
         */
        private function get($getter, $args)
        {
            $attributeValues = $this->getAttributeValues();
            if ($attributeValues === null || $attributeValues->isEmpty()) {
                return null;
            }
            $name = preg_replace('/^get/', '', $getter);
            $valueCollection = $this->filterCollection($attributeValues, $name);
            if ($valueCollection === null || $valueCollection->isEmpty()) {
                $valueCollection = $this->filterCollection($attributeValues, $this->decamelize($name));
                if ($valueCollection->isEmpty()) {
                    return null;
                }
            }


            //single value
            /** @var AttributeValue $attributeValueObject */
            $attributeValueObject = $valueCollection->first();
            $attribute = $attributeValueObject->getAttribute();
            if ($attribute->isMultiple()) {
                return $attribute->getValueCollection($attributeValueObject);
            }
            return $attribute->getSingleValue($attributeValueObject);
        }


        public function __call($name, $args)
        {
            if (StringUtils::startsWith($name, 'get')) {
                return $this->get($name, $args);
            }
            return null;
        }

        abstract public function getAttributeValues(): Collection;

        abstract public function setAttributeValues(Collection $attributeValues): void;

        //abstract public function addAttributeValue(AttributeValue $attributeValue): void;

        abstract public function removeAttributeValues(Collection $attributeValues): void;

        //abstract public function removeAttributeValue(AttributeValue $attributeValue): void;
    }