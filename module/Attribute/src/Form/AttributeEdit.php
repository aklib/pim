<?php
/**
 * Class AttributeEdit
 * @package Attribute\Form
 *
 * since: 15.10.2020
 * author: alexej@kisselev.de
 */

namespace Attribute\Form;


use Application\Form\EntityEdit;
use Application\ServiceManager\Interfaces\Constant;
use Application\ServiceManager\InvokableAwareFactory;
use Application\View\Helper\Form\FormCollection;
use Attribute\Entity\Attribute;
use Attribute\Entity\AttributeOption;
use Laminas\Form\Element;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;

class AttributeEdit extends EntityEdit
{

    protected function isPropertyShown($name): bool
    {
        $yes = parent::isPropertyShown($name);
        if (!$yes) {
            return false;
        }
        if ($name === 'attributeOptions') {
            if ($this->isNew()) {
                return false;
            }
            return $this->_entity->getType()->getType() === 'select';
        }
        return true;
    }

    /**
     * @param object $attribute
     */
    protected function postCreateForm(object $attribute): void
    {
        parent::postCreateForm($attribute);
        if ($this->isNew()) {
            return;
        }
        if ($attribute instanceof Attribute && $attribute->getType()->getType() === 'select') {
            // create attribute options tab
            $this->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, true);

            if (!$this->has('attributeOptions')) {
                return;
            }
            /** @var Select $element */
            $elementSelect = $this->get('attributeOptions');
            $this->remove($elementSelect->getName());

            $form = new EntityEdit();
            InvokableAwareFactory::aware($form);

            $containerFieldSet = new Fieldset('attributeOptions');
            // create an element for add new option
            foreach ($this->createOptionElement($form, null) as $el) {
                $containerFieldSet->add($el);
            }

            /** @var AttributeOption $attributeOption */
            foreach ($attribute->getAttributeOptions() as $attributeOption) {
                foreach ($this->createOptionElement($form, $attributeOption) as $el) {
                    $containerFieldSet->add($el);
                }
            }

            $containerFieldSet->setAttribute(Constant::HTML_ATTRIBUTE_DATA_TAB, 'Attribute Options');
            $containerFieldSet->setAttribute('id', 'attributeOptionsFieldset');
            $this->add($containerFieldSet);

            /** @var FormCollection $helper */
            $helper = $this->getServiceManager()->get('ViewHelperManager')->get('formCollection');
            if ($helper instanceof FormCollection) {
                $helper->setTemplate('attribute/partial/attribute-options');
            }
        }
    }

    /**
     * @param EntityEdit $form
     * @param AttributeOption|null $attributeOption
     * @return Element[]
     */
    private function createOptionElement(EntityEdit $form, AttributeOption $attributeOption = null): array
    {
        $isNew = $attributeOption === null;
        $attributeOption = $attributeOption ?? new AttributeOption();
        $form->createForm($attributeOption);
        $name = !$isNew ? 'attributeOptions[' . $attributeOption->getId() . ']' : 'attributeOptions[]';
        /** @var Element $element */
        $element = $form->get('name');
        $element->setName($name . '[' . $element->getName() . ']');
        $result = [$element];

        /** @var array $elementOptions */
        $elementOptions = $element->getOption('extended');
        $elementOptions['prepend'] = $this->getAddOnIconHTML('la la-exchange-alt la-rotate-90 draggable-handle', 'btn-secondary');
        $elementOptions['append'] = $this->getAddOnIconHTML('la la-times', 'btn-warning');
        $element->setOption('extended', $elementOptions);
        if ($isNew) {
            /*$element->setAttribute('disabled', true);*/
            $element->setAttribute('id', 'attributeOptionCloneMe');
        } else {
            $hidden = new Element\Hidden($name . '[id]');
            $hidden->setAttribute('value', $attributeOption->getId());
            $result[] = $hidden;
        }
        return $result;
    }

}