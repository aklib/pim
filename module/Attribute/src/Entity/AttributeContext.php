<?php /** @noinspection PhpUnused */


    namespace Attribute\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * AttributeContext
     * @ORM\NamedQueries({
     *   @ORM\NamedQuery(
     *      name = "dropdownChoice",
     *      query = "SELECT e.id, e.context FROM __CLASS__ e ORDER BY e.context DESC"
     *   )
     * })
     * @ORM\Table(name="attribute_context", uniqueConstraints={@ORM\UniqueConstraint(name="context", columns={"context"})})
     * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeContextDao")
     */
    class AttributeContext
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
         * @ORM\Column(name="context", type="string", length=16, nullable=false)
         */
        private string $context;

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
        public function getContext(): string
        {
            return $this->context;
        }

        /**
         * @param string $context
         */
        public function setContext(string $context): void
        {
            $this->context = $context;
        }
    }
