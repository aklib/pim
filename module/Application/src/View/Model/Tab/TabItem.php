<?php /** @noinspection PhpUnused */

    /**
     *
     * TabModel.php
     *
     * @since 21.05.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\View\Model\Tab;

    use Attribute\Entity\AttributeTab;
    use Laminas\View\Model\ModelInterface;

    class TabItem extends TabContainer
    {

        /**
         *
         * @var ModelInterface
         */
        protected ModelInterface $content;

        /**
         *
         * @var string
         */
        protected string $title;

        /**
         *
         * @var int
         */
        protected int $sortOrder = 0;

        /**
         *
         * @var AttributeTab
         */
        protected ?AttributeTab $attributeTab = null;

        /**
         *
         * @var string
         */
        protected ?string $href = null;

        /**
         *
         * @var TabContainer
         */
        protected TabContainer $parent;

        protected string $class = '';

        protected array $attributes = [];

        public function getTitle(): string
        {
            return $this->title;
        }

        public function setTitle($title): self
        {
            $this->title = $title;
            return $this;
        }

        public function getContent(): ModelInterface
        {
            return $this->content;
        }

        public function getSortOrder(): int
        {
            return $this->sortOrder;
        }

        public function getHref(): string
        {
            if ($this->href === null) {
                if ($this->parent !== null) {
                    //  subtab
                    $this->href = $this->parent->getHref() . '_' . $this->getSortOrder();
                } else {
                    $this->href = 'tab_' . rand(1, 100);
                }
            }
            return $this->href;
        }

        public function getParent(): TabContainer
        {
            return $this->parent;
        }

        public function setContent(ModelInterface $content): self
        {
            $this->content = $content;
            return $this;
        }

        public function setSortOrder($sortOrder): self
        {
            $this->sortOrder = $sortOrder;
            return $this;
        }

        public function setHref($href): self
        {
            $this->href = $href;
            return $this;
        }

        protected function setParent(TabContainer $parent): self
        {
            $this->parent = $parent;
            return $this;
        }

        /**
         * @return AttributeTab
         */
        public function getAttributeTab(): AttributeTab
        {
            return $this->attributeTab;
        }

        /**
         * @param AttributeTab $attributeTab
         * @return TabItem
         */
        public function setAttributeTab(AttributeTab $attributeTab): TabItem
        {
            $this->attributeTab = $attributeTab;
            return $this;
        }

        /**
         * @return array
         */
        public function getAttributes(): array
        {
            return $this->attributes;
        }

        /**
         * @param array $attributes
         * @return TabItem
         */
        public function setAttributes(array $attributes): TabItem
        {
            $this->attributes = $attributes;
            return $this;
        }

        /**
         * @param string $name
         * @param string $value
         * @return TabItem
         */
        public function addAttribute(string $name, string $value): TabItem
        {
            $this->attributes[$name] = $value;
            return $this;
        }


    }
