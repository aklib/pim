<?php

    namespace Product\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * ProductCreativeFile
     *
     * @ORM\Table(name="product_creative_file", indexes={@ORM\Index(name="creative_id", columns={"creative_id"})})
     * @ORM\Entity
     */
    class ProductCreativeFile
    {
        /**
         * @var int
         * @ORM\Column(name="id", type="integer", nullable=false)
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private int $id;

        /**
         * @var string
         * @ORM\Column(name="uri", type="string", length=255, nullable=false)
         */
        private string $uri;
        /**
         * @var int
         * @ORM\Column(name="width", type="integer", nullable=false)
         */
        private int $width;

        /**
         * @var int
         * @ORM\Column(name="height", type="integer", nullable=false)
         */
        private int $height;

        /**
         * @var DateTime|null
         *
         * @ORM\Column(name="created", type="datetime", nullable=true)
         */
        private ?DateTime $created;

        /**
         * @var int|null
         *
         * @ORM\Column(name="create_id", type="integer", nullable=true)
         */
        private ?int $createId;

        /**
         * @var DateTime|null
         *
         * @ORM\Column(name="changed", type="datetime", nullable=true)
         */
        private ?DateTime $changed;

        /**
         * @var int|null
         *
         * @ORM\Column(name="change_id", type="integer", nullable=true)
         */
        private ?int $changeId;
        /**
         * @var ProductCreative
         *
         * @ORM\ManyToOne(targetEntity="Product\Entity\ProductCreative", inversedBy="creativeFiles")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creative_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
         * })
         */
        private ProductCreative $creative;

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
        public function getUri(): string
        {
            return $this->uri;
        }

        /**
         * @param string $uri
         */
        public function setUri(string $uri): void
        {
            $this->uri = $uri;
        }

        /**
         * @return int
         */
        public function getWidth(): int
        {
            return $this->width;
        }

        /**
         * @param int $width
         */
        public function setWidth(int $width): void
        {
            $this->width = $width;
        }

        /**
         * @return int
         */
        public function getHeight(): int
        {
            return $this->height;
        }

        /**
         * @param int $height
         */
        public function setHeight(int $height): void
        {
            $this->height = $height;
        }

        /**
         * @return DateTime|null
         */
        public function getCreated(): ?DateTime
        {
            return $this->created;
        }

        /**
         * @param DateTime|null $created
         */
        public function setCreated(?DateTime $created): void
        {
            $this->created = $created;
        }

        /**
         * @return int|null
         */
        public function getCreateId(): ?int
        {
            return $this->createId;
        }

        /**
         * @param int|null $createId
         */
        public function setCreateId(?int $createId): void
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

        /**
         * @return ProductCreative
         */
        public function getCreative(): ProductCreative
        {
            return $this->creative;
        }

        /**
         * @param ProductCreative $creative
         */
        public function setCreative(ProductCreative $creative): void
        {
            $this->creative = $creative;
        }
    }
