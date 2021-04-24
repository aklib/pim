<?php

    namespace Product\Entity;

    use Application\Doctrine\Annotation as AppORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * ProductCreative
     * @ORM\Table(name="product_creative")
     * @ORM\Entity(repositoryClass="Product\Repository\ProductCreativeDao")
     */
    class ProductCreative
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
         * @ORM\Column(name="url", type="string", length=255, nullable=false)
         */
        private string $url;

        /**
         * @var string
         * @ORM\Column(name="title", type="string", length=32, nullable=false)
         */
        private string $title;

        /**
         * @var string
         * @ORM\Column(name="description", type="string", length=255, nullable=true)
         */
        private string $description;

        /**
         * @AppORM\Element(type="Laminas\Form\Element\File")
         * @ORM\OneToMany(targetEntity="Product\Entity\ProductCreativeFile", mappedBy="creative", cascade={"persist"}, orphanRemoval=true)
         */
        protected Collection $creativeFiles;

        public function __construct()
        {
            $this->creativeFiles = new ArrayCollection();
        }
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
        public function getUrl(): string
        {
            return $this->url;
        }

        /**
         * @param string $url
         */
        public function setUrl(string $url): void
        {
            $this->url = $url;
        }

        /**
         * @return string
         */
        public function getTitle(): string
        {
            return $this->title;
        }

        /**
         * @param string $title
         */
        public function setTitle(string $title): void
        {
            $this->title = $title;
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
         * @return Collection
         */
        public function getCreativeFiles(): Collection
        {
            return $this->creativeFiles;
        }

        /**
         * @param Collection $creativeFiles
         */
        public function setCreativeFiles(Collection $creativeFiles): void
        {
            $this->creativeFiles = $creativeFiles;
        }

        // ======================= METHODS REQUIRED FOR HYDRATION =======================

        /**
         * @param Collection $creativeFiles
         */
        public function removeCreativeFiles(Collection $creativeFiles): void
        {
            /** @var ProductCreativeFile $creativeFile */
            foreach ($creativeFiles as $creativeFile) {
                $this->removeCreativeFile($creativeFile);
            }
        }

        /**
         * @param ProductCreativeFile $creativeFile
         */
        public function removeCreativeFile(ProductCreativeFile $creativeFile): void
        {
            $this->creativeFiles->removeElement($creativeFile);
        }

        public function addCreativeFiles(Collection $creativeFiles): void
        {
            /** @var ProductCreativeFile $creativeFile */
            foreach ($creativeFiles as $creativeFile) {
                $this->addCreativeFile($creativeFile);
            }
        }

        /**
         * @param ProductCreativeFile $creativeFile
         */
        public function addCreativeFile(ProductCreativeFile $creativeFile): void
        {
            $creativeFile->setCreative($this);
            $this->creativeFiles->add($creativeFile);
        }


        // ======================= OTHER REQUIRED METHODS =======================
        public function __toString()
        {
            return $this->getTitle();
        }
    }
