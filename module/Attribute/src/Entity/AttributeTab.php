<?php /** @noinspection PhpUnused */

    namespace Attribute\Entity;

    use DateTime;
    use Doctrine\ORM\Mapping as ORM;


    /**
     * This class represents a attribute tab.
     * @ORM\NamedQueries({
     *   @ORM\NamedQuery(
     *      name = "dropdownChoice",
     *      query = "SELECT e.id, e.label FROM __CLASS__ e ORDER BY e.sortOrder ASC"
     *   )
     * })
     *
     * @ORM\Entity(repositoryClass="Attribute\Repository\TabDao")
     * @ORM\Table(name="attribute_tab")
     */
    class AttributeTab
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", nullable=false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private int $id;

        /**
         * @var string
         *
         * @ORM\Column(name="label", type="string", length=64, nullable=false)
         */
        private string $label;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_order", type="integer", nullable=false, options={"default"="1"})
         */
        private int $sortOrder = 1;

        /**
         *
         * @ORM\Column(name="created", type="datetime", nullable=true)
         */
        private ?DateTime $created;

        /**
         *
         * @ORM\Column(name="create_id", type="integer", nullable=true)
         */
        private ?int $createId;

        /**
         *
         * @ORM\Column(name="changed", type="datetime", nullable=true)
         */
        private ?DateTime $changed;

        /**
         *
         * @ORM\Column(name="change_id", type="integer", nullable=true)
         */
        private ?int $changeId;

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
        public function getLabel(): string
        {
            return $this->label;
        }

        /**
         * @param string $label
         */
        public function setLabel(string $label): void
        {
            $this->label = $label;
        }

        /**
         * @return int
         */
        public function getSortOrder(): int
        {
            return $this->sortOrder;
        }

        /**
         * @param int $sortOrder
         */
        public function setSortOrder(int $sortOrder): void
        {
            $this->sortOrder = $sortOrder;
        }

        /**
         * @return DateTime
         */
        public function getCreated(): DateTime
        {
            return $this->created;
        }

        /**
         * @param DateTime $created
         */
        public function setCreated(DateTime $created): void
        {
            $this->created = $created;
        }

        /**
         * @return int
         */
        public function getCreateId(): int
        {
            return $this->createId;
        }

        /**
         * @param int $createId
         */
        public function setCreateId(int $createId): void
        {
            $this->createId = $createId;
        }

        /**
         * @return DateTime|null
         */
        public function getChanged(): ?DateTime
        {
            return $this->changed;
        }

        /**
         * @param DateTime|null $changed
         */
        public function setChanged(?DateTime $changed): void
        {
            $this->changed = $changed;
        }

        /**
         * @return int|null
         */
        public function getChangeId(): ?int
        {
            return $this->changeId;
        }

        /**
         * @param int|null $changeId
         */
        public function setChangeId(?int $changeId): void
        {
            $this->changeId = $changeId;
        }
    }