<?php /** @noinspection PhpUnused */

namespace Attribute\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Attribute
 *
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *      name = "attributes",
 *      query = "SELECT e FROM __CLASS__ e ORDER BY e.sortOrder ASC"
 *   )
 * })
 * @ORM\Table(name="attribute", indexes={@ORM\Index(name="FK_attribute_attribute_context", columns={"context_id"}), @ORM\Index(name="FK_attribute_attribute_tab", columns={"tab_id"}), @ORM\Index(name="FK_attribute_attribute_type", columns={"type_id"}), @ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeDao")
 */
class Attribute
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
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private string $name;

    /**
     *
     * @ORM\Column(name="label", type="string", length=64, nullable=false)
     */
    private string $label;

    /**
     *
     * @ORM\Column(name="info_text", type="string", length=128, nullable=true)
     */
    private ?string $infoText;

    /**
     *
     * @ORM\Column(name="placeholder", type="string", length=64, nullable=true)
     */
    private ?string $placeholder;

    /**
     *
     * @ORM\Column(name="group_name", type="string", length=8, nullable=true)
     */
    private ?string $groupName;
    /**
     * @var bool
     *
     * @ORM\Column(name="multiple", type="boolean", nullable=false)
     */
    private bool $multiple;

    /**
     * @var bool
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private bool $required;

    /**
     *
     * @ORM\Column(name="pattern", type="string", length=64, nullable=true)
     */
    private string $pattern;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false, options={"default"="1"})
     */
    private int $sortOrder = 100;

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
     * @var AttributeContext
     *
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\AttributeContext", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="context_id", referencedColumnName="id", nullable=false)
     * })
     */
    private AttributeContext $context;

    /**
     * @var AttributeTab
     *
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\AttributeTab", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tab_id", referencedColumnName="id", nullable=false)
     * })
     */
    private AttributeTab $tab;

    /**
     * @var AttributeType
     *
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\AttributeType", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     * })
     */
    private AttributeType $type;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribute\Entity\AttributeOption", mappedBy="attribute", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     */
    protected Collection $attributeOptions;

    /**
     * Attribute constructor.
     */
    public function __construct()
    {
        $this->attributeOptions = new ArrayCollection();
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
     * @return string|null
     */
    public function getInfoText(): ?string
    {
        return $this->infoText;
    }

    /**
     * @param string|null $infoText
     */
    public function setInfoText(?string $infoText): void
    {
        $this->infoText = $infoText;
    }

    /**
     * @return string|null
     */
    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @param string|null $placeholder
     */
    public function setPlaceholder(?string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string|null
     */
    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    /**
     * @param string|null $groupName
     */
    public function setGroupName(?string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     */
    public function setMultiple(bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
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
     * @return AttributeContext
     */
    public function getContext(): AttributeContext
    {
        return $this->context;
    }

    /**
     * @param AttributeContext $context
     */
    public function setContext(AttributeContext $context): void
    {
        $this->context = $context;
    }

    /**
     * @return AttributeTab
     */
    public function getTab(): AttributeTab
    {
        return $this->tab;
    }

    /**
     * @param AttributeTab $tab
     */
    public function setTab(AttributeTab $tab): void
    {
        $this->tab = $tab;
    }

    /**
     * @return AttributeType
     */
    public function getType(): AttributeType
    {
        return $this->type;
    }

    /**
     * @param AttributeType $type
     */
    public function setType(AttributeType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collection
     */
    public function getAttributeOptions(): Collection
    {
        return $this->attributeOptions;
    }

    /**
     * @param Collection $attributeOptions
     */
    public function setAttributeOptions(Collection $attributeOptions): void
    {
        $this->attributeOptions = $attributeOptions;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function toMapping(): array
    {
        switch ($this->getType()->getType()) {
            case 'text':
                $length = 65536;
                break;
            case 'string':
            case 'url':
                $length = 255;
                break;
            case 'integer':
            case 'country':
            case 'float':
                $length = 11;
                break;
            case 'boolean':
                $length = 1;
                break;
            default:
                $length = 255;
        }
        return [
            'name'     => $this->getName(),
            'type'     => $this->getType()->getType(),
            'nullable' => !$this->isRequired(),
            'length'   => $length
        ];
    }

    // ======================= METHODS REQUIRED FOR HYDRATION =======================

    public function addAttributeOptions(Collection $attributeOptions): void
    {
        /** @var AttributeOption $attributeOption */
        foreach ($attributeOptions as $attributeOption) {
            $this->addAttributeOption($attributeOption);
        }
    }

    public function addAttributeOption(AttributeOption $attributeOption): void
    {
        $attributeOption->setAttribute($this);
        $this->attributeOptions->add($attributeOption);
    }

    public function removeAttributeOptions(Collection $attributeOptions): void
    {
        foreach ($attributeOptions as $attributeOption) {
            $this->removeAttributeOption($attributeOption);
        }
    }

    public function removeAttributeOption(AttributeOption $attributeOption): void
    {
        $this->attributeOptions->removeElement($attributeOption);
    }


    //================ HELP METHODS ================

    /**
     * @param AttributeValue $attributeValue
     * @return int|float|string|bool|null
     */
    public function getValueCollection(AttributeValue $attributeValue)
    {
        $getter = 'get' . ucfirst($attributeValue->getAttribute()->getType()->getField());
        return $attributeValue->$getter();
    }

    /**
     * @param AttributeValue $attributeValue
     * @return int|float|string|bool|null
     */
    public function getSingleValue(AttributeValue $attributeValue)
    {
        if ($this->isMultiple()) {
            throw new InvalidArgumentException(sprintf(
                '%s: the attribute flag must be NOT multiple', __METHOD__
            ));
        }
        /** @var Collection $valueCollection */
        $valueCollection = $this->getValueCollection($attributeValue);
        if ($valueCollection->isEmpty()) {
            return null;
        }
        if ($this->getType()->getType() === 'boolean') {
            return (bool)$valueCollection->first()->getVal();
        }
        return $valueCollection->first()->getVal();
    }
}
