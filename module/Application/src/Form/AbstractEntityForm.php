<?php

/**
 * Class AbstractEntityForm
 *
 * @since 01.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Form;

use Application\Entity\AbstractAttributeEntity;
use Application\Form\Factory\FormElementFactory;
use Application\Repository\AbstractDoctrineDao;
use Application\ServiceManager\Interfaces\Constant;
use Application\View\Manager\ViewManager;
use Attribute\Entity\Attribute;
use Attribute\Hydrator\AttributeEntityHydrator;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use InvalidArgumentException;
use Laminas\Form\Element;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset;
use Product\Controller\ProductController;

abstract class AbstractEntityForm extends AbstractAwareForm
{
    private int $elementPriority = 1000;
    private bool $attributable = false;
    protected ?object $_entity = null;

    /**
     * @param object|null $entity
     */
    public function createForm(?object $entity = null): void
    {
        if (empty($entity)) {
            //  needed for entity form
            throw new InvalidArgumentException(sprintf(
                '%s expects parameter to be entity instance, "%s" given', __METHOD__, (get_debug_type($entity))
            ));
        }
        $this->_entity = $entity;
        $this->setName('editForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form');
        $this->setAttribute('enctype', 'multipart/form-data');

        /** @var FormElementFactory $factory */
        $factory = $this->getServiceManager()->get(FormElementFactory::class);

        /** @var ClassMetadata $meta */
        $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($entity));
        $mappings = array_replace_recursive($classMetadata->fieldMappings, $classMetadata->associationMappings);
        $inputFilter = $this->getInputFilter();
        foreach ($mappings as $fieldName => $mapping) {
            if (!$this->isPropertyShown($fieldName)) {
                continue;
            }
            /** @var Element $element */
            $element = $factory->createFormElement($fieldName, get_class($entity));
            if ($element === null) {
                continue;
            }
            if (!$this->isNew()) {
                // avoid exception "...  must not be accessed before initialization"
                // init value(s)
                $value = $classMetadata->getFieldValue($entity, $fieldName);
                if (is_object($value)) {
                    if ($value instanceof Collection) {
                        // many to many and one to many
                        $ids = [];
                        foreach ($value as $reference) {
                            $identifierValues = $this->getEntityManager()->getClassMetadata(get_class($reference))->getIdentifierValues($reference);
                            $ids[] = array_shift($identifierValues);
                        }
                        if ($element instanceof Element\Select) {
                            $element->setAttribute('value', $ids);
                        } else {
                            $element->setAttribute('value', implode(', ', $ids));
                        }
                    } else {
                        // many_to_one, one_to_one
                        $identifierValues = $this->getEntityManager()->getClassMetadata(get_class($value))->getIdentifierValues($value);
                        if (count($identifierValues) === 1) {
                            //array(1) { ["id"]=> int(1) }
                            $element->setAttribute('value', array_shift($identifierValues));
                            //$element->setAttribute('required', true);
                        }
                    }
                } else {
                    // simple value
                    $element->setAttribute('value', $value);
                }

                $required = isset($mapping['nullable']) && $mapping['nullable'] === false && $mapping['type'] !== 'boolean';
                $element->setAttribute('required', $required);
                $factory->createInputFilter($mapping, $inputFilter, $fieldName);
            }
            $this->add($element, ['priority' => $this->getElementPriority($fieldName)]);
            if ($this->has($fieldName)) {
                $required = isset($mapping['nullable']) && $mapping['nullable'] === false && $mapping['type'] !== 'boolean';
                $element->setAttribute('required', $required);
                $factory->createInputFilter($mapping, $inputFilter, $fieldName);
                $this->postCreateElement($this->get($fieldName));
            }
        }

        if (!$this->isNew() && $entity instanceof AbstractAttributeEntity) {
            $this->setAttributable(true);
            $dao = $this->getEntityManager()->getRepository(get_class($entity));
            if ($dao instanceof AbstractDoctrineDao) {
                $this->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, true);
                /** @noinspection OneTimeUseVariablesInspection */
                $attributes = $dao->getNamedQueryResult(Constant::NAMED_QUERY_ATTRIBUTES);
                /** @var Attribute $attribute */
                foreach ($attributes as $attribute) {
                    $fieldName = $attribute->getName();
                    if (!$this->isPropertyShown($fieldName)) {
                        continue;
                    }
                    $element = $factory->createAttributeFormElement($attribute, get_class($entity));
                    if ($element === null) {
                        continue;
                    }
                    // set value
                    $getter = 'get' . $attribute->getName();
                    $value = $entity->$getter();

                    if ($value instanceof Collection) {
                        $ids = [];
                        foreach ($value as $item) {
                            $ids[] = $item->getVal()->getId();
                        }
                        $value = $ids;
                    }
                    if (is_object($value)) {
                        if ($element instanceof Fieldset) {
                            /** @var AttributeEntityHydrator $hydrator */
                            $hydrator = $this->getServiceManager()->get(AttributeEntityHydrator::class);
                            /** @var Element $el */
                            foreach ($hydrator->extract($value) as $key => $val) {
                                $elementName = $attribute->getName() . "[$key]";
                                if ($element->has($elementName)) {
                                    /** @var Element $el */
                                    $el = $element->get($elementName);
                                    if (!is_object($val)) {
                                        $el->setAttribute('value', $val);
                                    } elseif ($val instanceof Collection) {
                                        $ids = [];
                                        foreach ($val as $associationEntity) {
                                            if (method_exists($associationEntity, 'geId')) {
                                                $ids[] = $associationEntity->getId();
                                            }
                                        }
                                        $el->setAttribute('value', $ids);
                                        $el->setOption('value', $val);
                                    } else {
                                        $el->setAttribute('value', $val->getId());
                                    }
                                }
                            }
                        } else {
                            $identifierValues = $this->getEntityManager()->getClassMetadata(get_class($value))->getIdentifierValues($value);
                            if ($attribute->isMultiple()) {
                                $value = array_values($identifierValues);
                            } else {
                                $value = array_shift($identifierValues);
                            }
                        }
                    }

                    if (!$element instanceof Fieldset) {
                        $element->setValue($value);
                    }
                    /** @noinspection UnusedFunctionResultInspection */
                    $this->add($element, ['priority' => $this->getElementPriority($fieldName)]);
                    if ($this->has($fieldName)) {
                        $element->setAttribute('required', $attribute->isRequired());
                        $factory->createInputFilter($attribute->toMapping(), $inputFilter, $fieldName);
                        $this->postCreateElement($this->get($fieldName), $attribute);
                    }
                }
            }
        }

        $this->postCreateForm($entity);
        $this->setInitialized(true);
    }

    protected function isPropertyShown(string $name): bool
    {
        if (empty($name)) {
            return false;
        }
        switch ($name) {
            case 'id':
            case 'created':
            case 'changed':
            case 'createId':
            case 'changeId':
            case 'attributeValues':
                return false;
        }

        /** @var ViewManager $viewManager */
        $viewManager = $this->getServiceManager()->get(ViewManager::class);
        $controllerName = $viewManager->getControllerName();

        return $this->getAcl()->isAllowedColumn(null, $controllerName, $name);
    }

    /**
     * @return bool
     */
    public function isAttributable(): bool
    {
        return $this->attributable;
    }

    /**
     * @param bool $attributable
     */
    public function setAttributable(bool $attributable): void
    {
        $this->attributable = $attributable;
    }

    /**
     * @param string $fieldName
     * @return int
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getElementPriority(string $fieldName): int
    {
        $this->setElementPriority(10);

        return $this->elementPriority;
    }

    /**
     * @param int $elementPriority
     */
    public function setElementPriority(int $elementPriority): void
    {
        $this->elementPriority -= $elementPriority;
    }

    protected function isNew(): bool
    {
        return $this->getEntityManager()->getUnitOfWork()->getEntityState($this->_entity) === UnitOfWork::STATE_NEW;
    }

    /**
     * Gets element label. We uses translations
     * @param string $fieldName
     * @return string
     */
    protected function getElementLabel(string $fieldName): string
    {
        return $fieldName;
    }

    /**
     * post create form hook
     * @param object $entity
     */
    protected function postCreateForm(object $entity): void
    {
    }

    /**
     * @param ElementInterface $element
     * @param Attribute|null $attribute
     */
    protected function postCreateElement(ElementInterface $element): void
    {
    }
}
