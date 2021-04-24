<?php /** @noinspection PhpUnused */

    /**
     * Class Category
     * @package Category\Decorator
     *
     * since: 05.08.2020
     * author: alexej@kisselev.de
     */

    namespace Category\Decorator;


    use Application\Decorator\AbstractPrettyBootstrapElement;
    use Category\Entity\Category;
    use Category\Entity\AttributeCategory;

    /**
     * @method Category getObject()
     */
    class CategoryDecorator extends AbstractPrettyBootstrapElement
    {
        public function getTab(): string
        {
            return $this->getObject()->getTab()->getLabel();
        }

        public function getType(): string
        {
            return $this->getObject()->getType()->getDescription();
        }

        public function getContext(): string
        {
            return $this->getObject()->getContext()->getContext();
        }
    }
