<?php
    /**
     * Class ColumnEdit
     * @package Attribute\Form
     *
     * since: 09.08.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Form;

    use Attribute\Form\InputFilter\AttributeFilter;
    use Laminas\InputFilter\InputFilter;

    class EntityEdit extends AbstractEntityForm
    {
        protected function isPropertyShown($name): bool
        {
            $yes = parent::isPropertyShown($name);
            if (!$yes) {
                return false;
            }
            return true;
        }

        protected function postCreateForm(object $entity): void
        {
            parent::postCreateForm($entity);

            /** @var InputFilter $inputFilter */
            $inputFilter = $this->getInputFilter();
            //TODO: implement using laminas-filters
            $filter = new AttributeFilter();
            $filters = $filter->getAll();
            foreach ($filters as $filter) {
                if ($this->has($filter['name'])) {
                    $inputFilter->add($filter);
                }
            }
        }
    }