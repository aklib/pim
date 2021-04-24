<?php /** @noinspection PhpUnused */

    namespace Attribute\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * AttributeType
     * @ORM\NamedQueries({
     *   @ORM\NamedQuery(
     *      name = "dropdownChoice",
     *      query = "SELECT e.id, e.description FROM __CLASS__ e"
     *   )
     * })
     *
     * @ORM\Table(name="attribute_type")
     * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeTypeDao")
     */
    class AttributeType
    {
        /**
         *
         * @ORM\Column(name="id", type="integer", nullable=false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private int $id;

        /**
         *
         * @ORM\Column(name="type", type="string", length=16, nullable=false)
         */
        private string $type;

        /**
         *
         * @ORM\Column(name="element", type="string", length=16, nullable=false, options={"default"="text"})
         */
        private string $element = 'text';

        /**
         *
         * @ORM\Column(name="field", type="string", length=32, nullable=false, options={"default"="text"})
         */
        private string $field;

        /**
         *
         * @ORM\Column(name="description", type="string", length=128, nullable=true)
         */
        private string $description;

        /**
         * @return int
         */
        public function getId(): int
        {
            return $this->id;
        }

        /**
         * @param int $id
         */
        public function setId(int $id): void
        {
            $this->id = $id;
        }

        /**
         * @return string
         */
        public function getType(): string
        {
            return $this->type;
        }

        /**
         * @param string $type
         */
        public function setType(string $type): void
        {
            $this->type = $type;
        }

        /**
         * @return string
         */
        public function getElement(): string
        {
            return $this->element;
        }

        /**
         * @param string $element
         */
        public function setElement(string $element): void
        {
            $this->element = $element;
        }

        /**
         * @return string
         */
        public function getDescription(): string
        {
            return $this->description;
        }

        /**
         * @param string $description
         */
        public function setDescription(string $description): void
        {
            $this->description = $description;
        }

        /**
         * @return string
         */
        public function getField(): string
        {
            return $this->field;
        }

        /**
         * @param string $field
         */
        public function setField(string $field): void
        {
            $this->field = $field;
        }
    }
