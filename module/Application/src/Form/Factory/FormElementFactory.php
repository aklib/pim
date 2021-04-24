<?php /** @noinspection ContractViolationInspection */

/**
 * Class FormElementFactory
 * @package Application\Form\Factory
 * since: 17.07.2020
 * author: alexej@kisselev.de
 */

namespace Application\Form\Factory;

use Application\Form\EntityEdit;
use Application\Repository\AbstractDoctrineDao;
use Application\ServiceManager\Interfaces\Constant;
use Application\ServiceManager\InvokableAwareFactory;
use Attribute\Entity\Attribute;
use Attribute\Entity\AttributeOption;
use Attribute\Entity\AttributeValue;
use Doctrine\ORM\Mapping\MappingException;
use Exception;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Url;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;
use Product\Form\ProductEdit;

class FormElementFactory extends AbstractEntityFormElementFactory
{
    private array $attributes = [];

    /**
     * @param string $colName
     * @param string $entityClass
     * @return Element
     */
    public function createColumnFilter(string $colName, string $entityClass): ?Element
    {
        $this->setEntityName($entityClass);
        $classMetadata = $this->getEntityManager()->getClassMetadata($entityClass);
        // check if attribute
        if (!$classMetadata->hasField($colName) && !$classMetadata->hasAssociation($colName)) {
            $attribute = $this->getAttribute($colName);
            if ($attribute === null) {
                return null;
            }
            return $this->createAttributeColumnFilter($attribute, $entityClass);
        }

        $element = $this->createElement($colName, true);
        if (
            $element instanceof Email
            || $element instanceof Url

        ) {
            $element->setAttribute('type', 'text');
        }
        if ($element === null) {
            return null;
        }
        if ($element instanceof Checkbox) {
            $element = new Select($colName, $this->getElementOptions());
            $element->setValueOptions([
                '' => '',
                1  => 'Yes',
                0  => 'No'
            ]);
        }
        $this->bootstrapFilterElement($element);
        return $element;
    }

    public function createInputFilter(array $mapping, ?InputFilterInterface $inputFilter, string $fieldName): void
    {
        $filter = [];
        $validator = [];
        $required = isset($mapping['nullable']) && $mapping['nullable'] === false && $mapping['type'] !== 'boolean';
        if (array_key_exists('type', $mapping)) {
            switch ($mapping['type']) {
                case 'string':
                    $filter = [
                        ['name' => StringTrim::class],
                        ['name' => StripTags::class],
                        //['name' => Alnum::class],//not all strings must be alpha numeric
                    ];
                    $validator = [
                        [
                            'name'    => StringLength::class,
                            'options' => [
                                'min' => (isset($mapping['length']) && $mapping['length'] >= 2) ? 2 : 1,
                                'max' => $mapping['length'] ?? 255,
                            ],
                        ]
                    ];
                    break;
                case 'integer':
                    $filter = [
                        ['name' => ToInt::class],
                    ];
                    break;
                case 'float':
                    $filter = [
                        // don't work with decimal comma
                        //['name' => ToFloat::class],
                    ];
                    break;
            }
        }
        /** @noinspection NullPointerExceptionInspection */
        $inputFilter->add([
            'name'       => $fieldName,
            'required'   => $required,
            'filters'    => $filter,
            'validators' => $validator,
        ]);
    }

    /**
     * @param string $colName
     * @param string|null $entityClass
     * @return Element|null
     */
    public function createFormElement(string $colName, string $entityClass): ?Element
    {
        $this->setEntityName($entityClass);
        //dump("$entityClass::$colName");
        $element = $this->createElement($colName, false);
        if ($element === null) {
            return null;
        }
        $props = $this->getCustomElementMeta($colName);
        if (empty($props['tab'])) {
            $element->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, 'general');
        } else {
            $element->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, $props['tab']);
        }
        $element->setLabel($colName);
        $this->bootstrapElement($element);
        $classMetadata = $this->getEntityManager()->getClassMetadata($entityClass);
        try {
            $mapping = $classMetadata->getFieldMapping($colName);
            if ($mapping['type'] === 'string') {
                $element->setAttribute('maxlength', $mapping['length']);
                if ((int)$mapping['length'] < 255) {
                    $opt = $element->getOption('extended');
                    if (empty($opt['info'])) {
                        $opt['info'] = 'maximum string length is ' . $mapping['length'] . ' characters ';
                        $element->setOption('extended', $opt);
                    }
                }
            }
        } catch (MappingException $e) {
        }
        return $element;
    }

    private function bootstrapFilterElement(Element $element): void
    {
        $class = 'form-control form-control-sm';
        $multiple = (bool)$element->getAttribute('multiple') === true;
        if ($element instanceof Select && $multiple) {
            $class .= ' select2';
            $element->setAttribute('data-width', 'resolve')->
            setAttribute('style', 'width: 100%')->
            setAttribute('multiple', false);
        }
        $element->setAttribute('class', $class);
    }

    private function bootstrapElement(Element $element): void
    {
        if ($element instanceof Fieldset) {
            $class = 'form-group';
            /** @var Element $el */
            foreach ($element->getElements() as $el) {
                $this->bootstrapElement($el);
            }
        } elseif (!($element instanceof File)) {
            $element->setLabelAttributes(['class' => 'col-lg-3 col-form-label text-lg-right text-md-left text-sm-left']);
            $class = 'form-control';
            $multiple = (bool)$element->getAttribute('multiple') === true;
            if ($element instanceof Select && $multiple) {
                $class .= ' select2';
                $element->setAttribute('data-width', 'resolve')->
                setAttribute('style', 'width: 100%');
                //@TODO bootstrap 4 theme here
                //$element->setAttribute('data-theme','classic');
            }
        } else {
            $class = 'custom-file-input';
        }
        $element->setAttribute('class', $class);
    }

    /**
     * @param Attribute $attribute
     * @param string|null $entityClass
     * @return Element|null
     * @noinspection DuplicatedCode
     */
    public function createAttributeFormElement(Attribute $attribute, string $entityClass): ?Element
    {
        $this->setEntityName($entityClass);
        $name = $attribute->getName();
        $element = $this->getElementHTML5($name, $attribute->getType()->getElement(), false);
        if ($element === null) {
            switch ($attribute->getType()->getElement()) {
                case 'select':
                    $element = new Select($name, $this->getElementOptions());
                    $options = $this->getAttributeDropDownChoice($attribute);
                    $element->setValueOptions($options);
                    if ($attribute->isMultiple()) {
                        $element = $element->setAttribute('multiple', true);
                    }
                    break;
                case 'checkbox':
                    $element = new Checkbox($name, $this->getCheckboxOptions());
                    break;
                case 'textarea':
                    $element = new Textarea($name, $this->getElementOptions());
                    break;
                case 'fieldset':
                    $field = $attribute->getType()->getField();
                    $className = $this->getEntityManager()->getClassMetadata(AttributeValue::class)->getAssociationTargetClass($field);
                    $targetClass = $this->getEntityManager()->getClassMetadata($className)->getAssociationTargetClass('val');

                    $form = $name !== 'creative' ? new EntityEdit() : new ProductEdit();

                    InvokableAwareFactory::aware($form);

                    $form->createForm(new $targetClass());
                    $fieldSet = new Fieldset($name);

                    /** @var Element $el */
                    foreach ($form->getElements() as $el) {
                        $elName = $el->getName();
                        $el->setName($name . '[' . $elName . ']');
                        $fieldSet->add($el);
                    }
                    $element = $fieldSet;
                    break;
                default:
                    $element = new Text($name, $this->getElementOptions());
            }
        }

        if ($attribute->getInfoText() !== null) {
            $extended = $element->getOption('extended');
            $extended['info'] = $attribute->getInfoText();
            $element->setOption('extended', $extended);
        }

        if ($attribute->getPlaceholder() !== null) {
            $element = $element->setAttribute('placeholder', $attribute->getPlaceholder());
        }
        $groupName = $attribute->getGroupName();
        if (!empty($groupName)) {
            $element = $element->setAttribute(Constant::HTML_ATTRIBUTE_GROUP, $attribute->getGroupName());
        }
        $element->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, $attribute->getTab()->getLabel())
            ->setAttribute(Constant::HTML_ATTRIBUTE_DATA_ID, $attribute->getId());
        $element->setLabel($attribute->getLabel());
        $this->bootstrapElement($element);
        return $element;
    }

    /**
     * @param Attribute $attribute
     * @param string|null $entityClass
     * @return Element|null
     * @noinspection DuplicatedCode
     */
    public function createAttributeColumnFilter(Attribute $attribute, string $entityClass): ?Element
    {
        $this->setEntityName($entityClass);
        $name = $attribute->getName();
        $element = $this->getElementHTML5($name, $attribute->getType()->getElement(), true);
        if ($element === null) {
            switch ($attribute->getType()->getElement()) {
                case 'select':
                case 'fieldset':

                    $dropDownChoice = $this->getAttributeDropDownChoice($attribute);
                    if (!empty($dropDownChoice)) {
                        $element = new Select($name, $this->getElementOptions());
                        $options = array_replace_recursive(['' => ''], $this->getAttributeDropDownChoice($attribute));
                        $element->setValueOptions($options);
                        $element->setAttribute('multiple', false);
                    } else {
                        $element = new Text($name, $this->getElementOptions());
                    }
                    break;
                case 'checkbox':
                    $element = new Select($name, $this->getElementOptions());
                    $element->setValueOptions([
                        '' => '',
                        1  => 'yes',
                        0  => 'no'
                    ]);
                    break;
                default:
                    $element = new Text($name, $this->getElementOptions());
            }
        }


        $element->setLabel($attribute->getLabel());
        $this->bootstrapFilterElement($element);
        if ($element instanceof Select) {
            $class = $element->getAttribute('class');
            $class .= ' select2';
            $element->setAttribute('class', $class);
            $element->setAttribute('data-width', 'resolve');
            $element->setAttribute('style', 'width: 100%');
        }
        return $element;
    }

    private function getAttributeDropDownChoice(Attribute $attribute, $required = false): array
    {
        $options = $required ? [] : ['' => ''];
        if ($attribute->getType()->getType() !== 'select') {
            $field = $attribute->getType()->getField();
            $className = $this->getEntityManager()->getClassMetadata(AttributeValue::class)->getAssociationTargetClass($field);
            try {
                $targetClass = $this->getEntityManager()->getClassMetadata($className)->getAssociationTargetClass('val');
            } catch (Exception $e) {
                return [];
            }
            /** @var AbstractDoctrineDao $dao */
            $dao = $this->getEntityManager()->getRepository($targetClass);
            $options = [];
            try {
                $options = array_replace_recursive($options, $dao->getNamedQueryResult(Constant::NAMED_QUERY_DROPDOWN_CHOICE));
            } catch (Exception $e) {
            }
        } else {
            /** @var AttributeOption $attributeOption */
            foreach ($attribute->getAttributeOptions() as $attributeOption) {
                $options[$attributeOption->getId()] = $attributeOption->getName();
            }
        }
        return $options;
    }

    /**
     * Map all attributes by name
     * @param string $name
     * @return Attribute|null
     */
    protected function getAttribute(string $name): ?Attribute
    {
        if (empty($this->attributes)) {
            /** @var AbstractDoctrineDao $dao */
            $dao = $this->getEntityManager()->getRepository($this->getEntityName());
            /** @var Attribute $attribute */
            try {
                foreach ($dao->getNamedQueryResult(Constant::NAMED_QUERY_ATTRIBUTES) as $attribute) {
                    $this->attributes[$attribute->getName()] = $attribute;
                }
            } catch (Exception $e) {
            }
        }
        return $this->attributes[$name] ?? null;
    }
}