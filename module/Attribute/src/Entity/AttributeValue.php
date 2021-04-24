<?php /** @noinspection PhpUnused */

namespace Attribute\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeValue
 *
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\Table(name="attribute_value", uniqueConstraints={@ORM\UniqueConstraint(name="reference_id", columns={"reference_id", "attribute_id"})}, indexes={@ORM\Index(name="discr", columns={"discr"}), @ORM\Index(name="FK_attribute_value_attribute", columns={"attribute_id"}), @ORM\Index(name="IDX_FE4FBB821645DEA9", columns={"reference_id"})})
 * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeValueDao")
 */
class AttributeValue
{
    /**
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected ?DateTime $created;

    /**
     *
     * @ORM\Column(name="create_id", type="integer", nullable=true)
     */
    protected ?int $createId;

    /**
     *
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     */
    protected ?DateTime $changed;

    /**
     *
     * @ORM\Column(name="change_id", type="integer", nullable=true)
     */
    protected ?int $changeId;

    /**
     *
     * @ORM\Column(name="attribute_id", type="integer", nullable=false)
     */
    protected int $attributeId;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\Attribute", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected Attribute $attribute;
    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueString", mappedBy="value", cascade={"persist"}, orphanRemoval=true)
     */
    protected Collection $valueStrings;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueFloat", mappedBy="value", cascade={"persist"}, orphanRemoval=true)
     */
    protected Collection $valueFloats;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueText", mappedBy="value", cascade={"persist"}, orphanRemoval=true)
     */
    protected Collection $valueTexts;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueNumber", mappedBy="value", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected Collection $valueNumbers;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueCountry", mappedBy="value", cascade={"persist","remove"}, orphanRemoval=true)
     */
    protected Collection $valueCountries;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueSelect", mappedBy="value", cascade={"persist"}, orphanRemoval=true)
     */
    protected Collection $valueSelects;

    /**
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeValueCreative", mappedBy="value", cascade={"persist","remove"}, orphanRemoval=true)
     */
    protected Collection $valueCreatives;

    /**
     * @ORM\Column(name="reference_id", type="integer", nullable=false)
     */
    protected int $referenceId;

    public function __construct()
    {
        $this->valueCountries = new ArrayCollection();
        $this->valueCreatives = new ArrayCollection();
        $this->valueStrings = new ArrayCollection();
        $this->valueNumbers = new ArrayCollection();
        $this->valueFloats = new ArrayCollection();
        $this->valueTexts = new ArrayCollection();
        $this->valueSelects = new ArrayCollection();
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
     * @return int
     */
    public function getReferenceId(): int
    {
        return $this->referenceId;
    }

    /**
     * @param int $referenceId
     */
    public function setReferenceId(int $referenceId): void
    {
        $this->referenceId = $referenceId;
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

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    /**
     * @param int $attributeId
     */
    public function setAttributeId(int $attributeId): void
    {
        $this->attributeId = $attributeId;
    }

    /**
     * @return Collection
     */
    public function getValueStrings(): Collection
    {
        return $this->valueStrings;
    }

    /**
     * @param Collection $valueStrings
     */
    public function setValueStrings(Collection $valueStrings): void
    {
        $this->valueStrings = $valueStrings;
    }

    /**
     * @return Collection
     */
    public function getValueFloats(): Collection
    {
        return $this->valueFloats;
    }

    /**
     * @param Collection $valueFloats
     */
    public function setValueFloats(Collection $valueFloats): void
    {
        $this->valueFloats = $valueFloats;
    }

    /**
     * @return Collection
     */
    public function getValueTexts(): Collection
    {
        return $this->valueTexts;
    }

    /**
     * @param Collection $valueTexts
     */
    public function setValueTexts(Collection $valueTexts): void
    {
        $this->valueTexts = $valueTexts;
    }

    /**
     * @return Collection
     */
    public function getValueNumbers(): Collection
    {
        return $this->valueNumbers;
    }

    /**
     * @param Collection $valueNumbers
     */
    public function setValueNumbers(Collection $valueNumbers): void
    {
        $this->valueNumbers = $valueNumbers;
    }

    /**
     * @return Collection
     */
    public function getValueCountries(): Collection
    {
        return $this->valueCountries;
    }

    /**
     * @param Collection $valueCountries
     */
    public function setValueCountries(Collection $valueCountries): void
    {
        $this->valueCountries = $valueCountries;
    }

    /**
     * @return Collection
     */
    public function getValueCreatives(): Collection
    {
        return $this->valueCreatives;
    }

    /**
     * @param Collection $valueCreatives
     */
    public function setValueCreatives(Collection $valueCreatives): void
    {
        $this->valueCreatives = $valueCreatives;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getValueSelects(): Collection
    {
        return $this->valueSelects;
    }

    /**
     * @param ArrayCollection|Collection $valueSelects
     */
    public function setValueSelects(Collection $valueSelects): void
    {
        $this->valueSelects = $valueSelects;
    }


// ======================= METHODS REQUIRED FOR HYDRATION =======================
    public function addValueTexts(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueText($value);
        }
    }

    public function addValueText(AttributeValueText $value): void
    {
        $value->setValue($this);
        $this->valueTexts->add($value);
    }

    public function removeValueTexts(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueText($value);
        }
    }

    public function removeValueText(AttributeValueText $value): void
    {
        $value->setValue(null);
        $this->valueTexts->removeElement($value);
    }

//-----------------------------------------------------
    public function addValueStrings(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueString($value);
        }
    }

    public function addValueString(AttributeValueString $value): void
    {
        $value->setValue($this);
        $this->valueStrings->add($value);
    }

    public function removeValueStrings(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueString($value);
        }
    }

    public function removeValueString(AttributeValueString $value): void
    {
        $value->setValue(null);
        $this->valueStrings->removeElement($value);
    }

    //-----------------------------------------------------
    public function addValueNumbers(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueNumber($value);
        }
    }

    public function addValueNumber(AttributeValueNumber $value): void
    {
        $value->setValue($this);
        $this->valueNumbers->add($value);
    }

    public function removeValueNumbers(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueNumber($value);
        }
    }

    public function removeValueNumber(AttributeValueString $value): void
    {
        //$value->setValue(null);
        $this->valueNumbers->removeElement($value);
    }

    //-----------------------------------------------------
    public function addValueFloats(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueFloat($value);
        }
    }

    public function addValueFloat(AttributeValueFloat $value): void
    {
        $value->setValue($this);
        $this->valueFloats->add($value);
    }

    public function removeValueFloats(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueFloat($value);
        }
    }

    public function removeValueFloat(AttributeValueString $value): void
    {
        //$value->setValue(null);
        $this->valueFloats->removeElement($value);
    }

    //-----------------------------------------------------
    public function addValueCountries(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueCountry($value);
        }
    }

    public function addValueCountry(AttributeValueCountry $value): void
    {
        $value->setValue($this);
        $this->valueCountries->add($value);
    }

    public function removeValueCountries(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueCountry($value);
        }
    }

    public function removeValueCountry(AttributeValueCountry $value): void
    {
        //$value->setValue(null);
        $this->valueCountries->removeElement($value);
    }

    //-----------------------------------------------------
    public function addValueCreatives(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueCreative($value);
        }
    }

    public function addValueCreative(AttributeValueCreative $value): void
    {
        $value->setValue($this);
        $this->valueCreatives->add($value);
    }

    public function removeValueCreatives(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueCreative($value);
        }
    }

    public function removeValueCreative(AttributeValueCreative $value): void
    {
        $this->valueCreatives->removeElement($value);
    }

    //-----------------------------------------------------
    public function addValueSelects(Collection $values): void
    {
        foreach ($values as $value) {
            $this->addValueSelect($value);
        }
    }

    public function addValueSelect(AttributeValueSelect $value): void
    {
        $value->setValue($this);
        $this->valueSelects->add($value);
    }

    public function removeValueSelects(Collection $values): void
    {
        foreach ($values as $value) {
            $this->removeValueSelect($value);
        }
    }

    public function removeValueSelect(AttributeValueSelect $value): void
    {
        $this->valueSelects->removeElement($value);
    }
}
