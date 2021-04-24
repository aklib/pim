<?php /** @noinspection PhpUnused */

    /**
     * Class Attribute
     * @package Attribute\Decorator
     *
     * since: 05.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Decorator;


    use Application\Decorator\AbstractPrettyBootstrapElement;
    use Attribute\Entity\Attribute;
    use Attribute\Entity\AttributeCategory;

    /**
     * @method Attribute getObject()
     */
    class AttributeDecorator extends AbstractPrettyBootstrapElement
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
