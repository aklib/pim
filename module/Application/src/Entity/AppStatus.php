<?php

    /**
     *
     * Status.php
     *
     * @since 14.06.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * Status
     * @ORM\NamedQueries({
     *   @ORM\NamedQuery(
     *     name = "dropdownChoice",
     *     query = "SELECT e.id, e.name FROM __CLASS__ e ORDER BY e.name ASC"
     *   )
     * })
     * @ORM\Table(name="app_status")
     * @ORM\Entity(repositoryClass="Application\Repository\StatusDao")
     */
    class AppStatus
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
         * @ORM\Column(name="name", type="string", length=255, nullable=false)
         */
        private string $name;

        /**
         * @var string
         *
         * @ORM\Column(name="description", type="string", length=255, nullable=false)
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
        public function getName(): string
        {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName(string $name): void
        {
            $this->name = $name;
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
    }
