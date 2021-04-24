<?php
/**
 * Class AttributeOption
 * @package Attribute\Entity
 *
 * since: 15.10.2020
 * author: alexej@kisselev.de
 */

namespace Attribute\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AttributeOption  * @package Attribute\Entity
 *
 * @since: 15.10.2020
 * @author: alexej@kisselev.de
 *
 * @ORM\Table(name="attribute_option", uniqueConstraints={@ORM\UniqueConstraint(name="attribute_id_name", columns={"attribute_id", "name"})}, indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="IDX_78672EEAB6E62EFA", columns={"attribute_id"})})
 * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeOptionDao")
 */
class AttributeOption
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
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private string $name;

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
     *
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\Attribute",inversedBy="attributeOptions", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Attribute $attribute;

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
     * @return Attribute
     */
    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function __toString()
    {
        return $this->getName();
    }
}