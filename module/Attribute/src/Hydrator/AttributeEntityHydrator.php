<?php /** @noinspection TransitiveDependenciesUsageInspection */

/**
 * Class AttributeEntityHydrator
 * @package Attribute\Hydrator
 *
 * since: 13.08.2020
 * author: alexej@kisselev.de
 */

namespace Attribute\Hydrator;


use Application\Entity\AbstractAttributeEntity;
use Application\Repository\AbstractDoctrineDao;
use Application\ServiceManager\Interfaces\AuthenticationAware;
use Application\ServiceManager\Interfaces\Constant;
use Attribute\Entity\Attribute;
use Attribute\Entity\AttributeValue;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Inflector\Inflector;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Exception;
use User\Entity\User;

class AttributeEntityHydrator extends DoctrineObject implements AuthenticationAware
{
    private ?AbstractAttributeEntity $entity = null;
    private ?Collection $attributes = null;
    private DoctrineObject $valueHydrator;
    private User $user;

    /**
     * AttributeEntityHydrator constructor.
     * @param ObjectManager $objectManager
     * @param bool $byValue
     * @param Inflector|null $inflector
     */
    public function __construct(ObjectManager $objectManager, $byValue = true, Inflector $inflector = null)
    {
        parent::__construct($objectManager, $byValue, $inflector);
        $this->valueHydrator = new DoctrineObject($objectManager, $byValue, $inflector);
    }

    /**
     * Override to implement attributes
     * @param array $data
     * @param object $object
     * @return object|null
     */
    public function hydrate(array $data, object $object)
    {
        if ($object instanceof AbstractAttributeEntity) {
            $this->entity = $object;
        } else {
            $this->entity = null;
        }
        return parent::hydrate($data, $object);
    }

    /**
     * Override parent method to implement attributes
     * @param string $name
     * @param mixed $value
     * @param array|null $data
     * @return bool|DateTime|float|int|mixed|string|null
     */
    public function hydrateValue(string $name, $value, ?array $data = null)
    {
        if ($this->metadata->hasField($name) || $this->metadata->hasAssociation($name)) {
            return parent::hydrateValue($name, $value, $data);
        }
        if ($this->entity === null) {
            return parent::hydrateValue($name, $value, $data);
        }
        try {
            return $this->hydrateAttribute($name, $value);
        } catch (Exception $e) {
        }
        return null;
    }

    /**
     * Saving an attribute value
     * @param string $name
     * @param $value
     * @param array|null $data
     * @return array|string|float|null
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    protected function hydrateAttribute(string $name, $value, ?array $data = null)
    {
        /** @var Attribute $attribute */
        $attribute = $this->getAttribute($name);
        if ($attribute === null) {
            return $value;
        }

        if (!$attribute->isMultiple()) {
            if ($value === null) {
                $value = '';
            }
            if (is_array($value)) {
                $ret = $this->setSingleValueArray($value, $attribute);
            } else {
                $ret = $this->setSingleValue($value, $attribute);
            }
        } else {
            $ret = $this->setMultiValue($value, $attribute);
        }

        //return !$attribute->isMultiple() ? $this->setSingleValue($value, $attribute) : $this->setMultiValue($value, $attribute);
        return $ret;
    }

    /**
     * Set not multiple value
     * @param string $value
     * @param Attribute $attribute
     * @return bool|DateTime|float|int|string|null
     */
    protected function setSingleValue(string $value, Attribute $attribute)
    {
        $attributeValue = $this->getAttributeValue($attribute);

        if (!is_numeric($value) && empty($value)) {
            // parameter for attributeValues AbstractAttributeEntity instances must be orphanRemoval=true
            $this->entity->getAttributeValues()->removeElement($attributeValue);
            $attributeValue = null;
            return $value;
        }
        // getValueTexts|getValueStrings etc.
        $getter = 'get' . ucfirst($attribute->getType()->getField());
        /** @var Collection $targetCollection */
        $targetCollection = $attributeValue->$getter();
        $savedVal = $value;
        if ($targetCollection->isEmpty()) {
            $targetEntity = $this->createTargetEntity($attribute);
            $targetCollection->add($targetEntity);
            $add = 'add' . ucfirst($attribute->getType()->getField());
            $attributeValue->$add($targetCollection);
        } else {
            $targetEntity = $targetCollection->first();
            $savedVal = $targetEntity->getVal();
        }
        $normalizedValue = $this->handleTypeConversions($value, $attribute->getType()->getType());

        $this->valueHydrator->hydrate(['val' => $normalizedValue], $targetEntity);
        if ($savedVal !== $targetEntity->getVal()) {
            $this->setUpdated($targetEntity);
        }

        return $normalizedValue;
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    protected function setMultiValue(?array $values, Attribute $attribute)
    {
        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->getAttributeValue($attribute);
        if (empty($values)) {
            // parameter for attributeValues AbstractAttributeEntity instances must be orphanRemoval=true
            $this->entity->getAttributeValues()->removeElement($attributeValue);
            $attributeValue = null;
            return $values;
        }
        // getValueTexts|getValueStrings etc.
        $getter = 'get' . ucfirst($attribute->getType()->getField());
        /** @var Collection $targetCollection */
        $targetCollection = $attributeValue->$getter();
        $meta = $this->getTargetMapping($attribute);
        $ids = array_map('intval', $values);
        if ($meta->hasAssociation('val')) {
            $deleteCollection = $targetCollection->filter(static function ($element) use ($ids) {
                // filter by unique key
                if (!in_array($element->getVal()->getId(), $ids, true)) {
                    return $element;
                }
            });
            $remove = 'remove' . ucfirst($attribute->getType()->getField());
            $attributeValue->$remove($deleteCollection);
        } else {
            // only associations like country
            return [];
        }
        $savedIds = [];
        foreach ($targetCollection as $element) {
            $savedIds[] = $element->getVal()->getId();
        }
        $toCreate = array_diff($ids, $savedIds);

        if (!empty($toCreate)) {
            $toCreateCollection = new ArrayCollection();
            foreach ($toCreate as $id) {
                $targetEntity = $this->createTargetEntity($attribute);
                $toCreateCollection->add($targetEntity);
                $this->valueHydrator->hydrate(['val' => $id], $targetEntity);
            }
            $add = 'add' . ucfirst($attribute->getType()->getField());
            $attributeValue->$add($toCreateCollection);
        }
        return $toCreate;
    }

    /**
     * Find attribute by name
     * @param string $name
     * @return Attribute|null
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function getAttribute(string $name): ?Attribute
    {
        if ($this->attributes === null) {
            /** @var AbstractDoctrineDao $dao */
            $dao = $this->objectManager->getRepository($this->metadata->getName());
            $attributes = $dao->getNamedQueryResult(Constant::NAMED_QUERY_ATTRIBUTES);
            $this->attributes = new ArrayCollection($attributes);
        }
        /** @var Collection $collection */
        $collection = $this->attributes->filter(static function (Attribute $element) use ($name) {
            // filter by unique key
            if (strcasecmp($element->getName(), $name) === 0) {
                return $element;
            }
        });
        if ($collection->isEmpty()) {
            return null;
        }
        return $collection->first();
    }

    /**
     * Find attribute value(s)
     * @param Attribute $attribute
     * @return AttributeValue|null
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function getAttributeValue(Attribute $attribute): AttributeValue
    {
        $collection = $this->entity->getAttributeValues()->filter(static function (AttributeValue $element) use ($attribute) {
            // filter by unique key
            if ($element->getAttribute()->getId() === $attribute->getId()) {
                return $element;
            }
        });
        if ($collection->isEmpty()) {
            return $this->createAttributeValue($attribute);
        }
        return $collection->first();
    }

    protected function handleTypeConversions($value, $typeOfField)
    {
        if ($typeOfField === 'float') {
                $value = str_replace(',', '.', $value);
        }
        return parent::handleTypeConversions($value, $typeOfField);
    }

    protected function getTargetMapping(Attribute $attribute): ClassMetadata
    {
        $mappings = $this->objectManager->getClassMetadata(AttributeValue::class)->associationMappings;
        $targetEntityClass = $mappings[$attribute->getType()->getField()]['targetEntity'];
        return $this->objectManager->getClassMetadata($targetEntityClass);
    }

    /**
     * Creates an attribute value target (float, string etc.)
     * @param Attribute $attribute
     * @return object
     */
    protected function createTargetEntity(Attribute $attribute): object
    {
        $targetEntityClass = $this->getTargetMapping($attribute)->getName();
        $instance = new $targetEntityClass();
        $this->setCreated($instance);
        return $instance;
    }

    /**
     * Creates an attribute value and adds to entity value collection
     * @param Attribute $attribute
     * @return AttributeValue
     */
    private function createAttributeValue(Attribute $attribute): AttributeValue
    {
        $attributeValueClass = $this->metadata->getAssociationTargetClass('attributeValues');
        $attributeValue = new $attributeValueClass();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setReference($this->entity);
        $this->setCreated($attributeValue);
        $this->objectManager->persist($attributeValue);
        $this->entity->addAttributeValue($attributeValue);
        return $attributeValue;
    }

    /**
     * Sets date and current user for created data
     * @param object $object
     */
    private function setCreated(object $object): void
    {
        if (method_exists($object, 'setCreateId')) {
            $object->setCreateId($this->getCurrentUser()->getId());
        }
        if (method_exists($object, 'setCreated')) {
            $object->setCreated(new DateTime());
        }
    }

    /**
     * Sets date and current user for updated data
     * @param object $object
     */
    private function setUpdated(object $object): void
    {
        if (method_exists($object, 'setChangeId')) {
            $object->setChangeId($this->getCurrentUser()->getId());
        }
        if (method_exists($object, 'setChanged')) {
            $object->setChanged(new DateTime());
        }
    }

    /**
     * Interface AuthenticationAware implementation
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->user;
    }

    /**
     * Interface AuthenticationAware implementation
     * @param User $user
     */
    public function setCurrentUser(User $user): void
    {
        $this->user = $user;
    }

    private function setSingleValueArray(array $value, Attribute $attribute): array
    {
        $attributeValue = $this->getAttributeValue($attribute);

        if (empty($value)) {
            // parameter for attributeValues AbstractAttributeEntity instances must be orphanRemoval=true
            $this->entity->getAttributeValues()->removeElement($attributeValue);
            $attributeValue = null;
            return $value;
        }
        // getValueTexts|getValueStrings etc.
        $getter = 'get' . ucfirst($attribute->getType()->getField());
        /** @var Collection $targetCollection */
        $targetCollection = $attributeValue->$getter();

        if ($targetCollection->isEmpty()) {
            $targetEntity = $this->createTargetEntity($attribute);
            $targetReferenceClass = $this->objectManager->getClassMetadata(get_class($targetEntity))->getAssociationTargetClass('val');
            $targetReferenceInstance = new $targetReferenceClass();
            $this->setUpdated($targetReferenceInstance);
            $this->valueHydrator->hydrate($value, $targetReferenceInstance);
            $this->objectManager->persist($targetReferenceInstance);
            $this->valueHydrator->hydrate(['val' => $targetReferenceInstance], $targetEntity);
            $targetCollection->add($targetEntity);
            $add = 'add' . ucfirst($attribute->getType()->getField());
            $attributeValue->$add($targetCollection);
        } else {
            $targetEntity = $targetCollection->first();
            $targetReferenceInstance = $targetEntity->getVal();
            $this->valueHydrator->hydrate($value, $targetReferenceInstance);
        }
        $this->setUpdated($targetEntity);

        return $value;
    }
}
