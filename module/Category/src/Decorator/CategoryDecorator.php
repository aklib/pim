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
        public function getRoot(): string
        {
            return $this->getObject()->getName();
        }

        public function getParent(): string
        {
            $parent = $this->getObject()->getParent();
            if($parent === null){
                return 'root';
            }
            return $parent->getName();
        }
    }
