<?php

namespace Attribute\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Product\Entity\ProductCreative;

/**
 * AttributeValueCreative
 *
 * @ORM\Table(name="attribute_value_creative", indexes={@ORM\Index(name="IDX_2B182F4597DFBA65", columns={"val"}), @ORM\Index(name="IDX_2B182F45F920BBA2", columns={"value_id"})})
 * @ORM\Entity
 */
class AttributeValueCreative
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private ?DateTime $created;

    /**
     * @ORM\Column(name="create_id", type="integer", nullable=true)
     */
    private ?int $createId;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     */
    private ?DateTime $changed;

    /**
     * @ORM\Column(name="change_id", type="integer", nullable=true)
     */
    private ?int $changeId;

    /**
     * @ORM\OneToOne(targetEntity="Product\Entity\ProductCreative", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="val", referencedColumnName="id")
     */
    private ProductCreative $val;

    /**
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\AttributeValue", inversedBy="valueCreatives", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="value_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private AttributeValue $value;

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
    public function getVal(): ProductCreative
    {
        return $this->val;
    }

    /**
     * @param ProductCreative $val
     */
    public function setVal(ProductCreative $val): void
    {
        $this->val = $val;
    }

    /**
     * @return AttributeValue
     */
    public function getValue(): AttributeValue
    {
        return $this->value;
    }

    /**
     * @param AttributeValue $value
     */
    public function setValue(AttributeValue $value): void
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->getVal()->getName();
    }
}
