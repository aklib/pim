<?php /** @noinspection PhpUnused */


    namespace Application\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * AppSequenceId
     *
     * @ORM\Table(name="app_sequence_id")
     * @ORM\Entity
     */
    class AppSequenceId
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
         * @ORM\Column(name="name", type="string", length=128, nullable=false)
         */
        private string $name;

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

    }
