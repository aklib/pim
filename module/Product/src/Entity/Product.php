<?php /** @noinspection PhpUnused */

namespace Product\Entity;

use Application\Custom\ResultRow;
use Application\Entity\AbstractAttributeEntity;
use Application\Entity\AppCountry;
use Application\Entity\AppStatus;
use Attribute\Entity\AttributeValue;
use Company\Entity\Advertiser;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered product.
 *
 * @method AppCountry getCountry()
 * @method float getBid()
 * @method float getDailyBudget()
 * @method float getTotalBudget()
 * @method ProductCreative getCreative()
 * @method int getClickFrequency()
 * @method int getFrequencyCapping()
 * @method Collection getUserQuality()
 * @method Collection getBrowser()
 * @method Collection getPlatform()
 * @method Collection getCategory()
 * @method string getFeed()
 *
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *      name = "dropdownChoice",
 *      query = "SELECT e.id, e.name FROM __CLASS__ e ORDER BY e.name ASC"
 *   ),
 *   @ORM\NamedQuery(
 *      name = "attributeColumns",
 *      query = "SELECT e.id, e.name, e.sortOrder, e.label, t.type FROM Attribute\Entity\Attribute e INNER JOIN e.context c INNER JOIN e.type t WHERE c.id=3 ORDER BY e.sortOrder ASC"
 *   ),
 *      @ORM\NamedQuery(
 *      name = "attributes",
 *      query = "SELECT e FROM Attribute\Entity\Attribute e INNER JOIN e.context c WHERE c.context='product' ORDER BY e.sortOrder ASC"
 *   )
 * })
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="FK_D34A04ADC54C8C93", columns={"type_id"}), @ORM\Index(name="FK_product_company_advertiser", columns={"advertiser_id"}), @ORM\Index(name="IDX_D34A04AD6BF700BD", columns={"status_id"})})
 * @ORM\Entity(repositoryClass="Product\Repository\ProductDao")
 */
class Product extends AbstractAttributeEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Application\Entity\Generator\SequenceGenerator")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @var ProductType
     *
     * @ORM\ManyToOne(targetEntity="Product\Entity\ProductType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private ProductType $type;

    /**
     * @var AppStatus
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\AppStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=false)
     * })
     */
    private AppStatus $status;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Product\Entity\ProductAttributeValue", mappedBy="reference", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private Collection $attributeValues;

    /**
     * @var Advertiser
     *
     * @ORM\ManyToOne(targetEntity="Company\Entity\Advertiser", inversedBy="campaigns")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="advertiser_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Advertiser $advertiser;

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
     * @var ResultRow|null
     */
    private ?ResultRow $statistics = null;

    /**
     * @var ResultRow|null
     */
    private ?ResultRow $statisticsToday = null;

    public function __construct()
    {
        $this->attributeValues = new ArrayCollection();
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
     * @return AppStatus
     */
    public function getStatus(): AppStatus
    {
        return $this->status;
    }

    /**
     * @param AppStatus $status
     */
    public function setStatus(AppStatus $status): void
    {
        $this->status = $status;
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
     * @return Advertiser
     */
    public function getAdvertiser(): Advertiser
    {
        return $this->advertiser;
    }

    /**
     * @param Advertiser $advertiser
     * @return Product
     */
    public function setAdvertiser(Advertiser $advertiser): Product
    {
        $this->advertiser = $advertiser;
        return $this;
    }

    /**
     * @return ProductType
     */
    public function getType(): ProductType
    {
        return $this->type;
    }

    /**
     * @param ProductType $type
     */
    public function setType(ProductType $type): void
    {
        $this->type = $type;
    }

    // ======================= METHODS REQUIRED FOR HYDRATION =======================

    /**
     * @param Collection $attributeValues
     */
    public function removeAttributeValues(Collection $attributeValues): void
    {
        /** @var AttributeValue $attributeValue */
        foreach ($attributeValues as $attributeValue) {
            $this->removeAttributeValue($attributeValue);
        }
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function removeAttributeValue(AttributeValue $attributeValue): void
    {
        $this->attributeValues->removeElement($attributeValue);
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function addAttributeValue(AttributeValue $attributeValue): void
    {

        $this->attributeValues->add($attributeValue);
    }

    public function getAttributeValues(): Collection
    {
        return $this->attributeValues;
    }

    public function setAttributeValues(Collection $attributeValues): void
    {
        /** @var ProductAttributeValue $attributeValue */
        foreach ($attributeValues as $attributeValue) {
            $this->addAttributeValue($attributeValue);
        }
    }

    /**
     * @return ResultRow
     */
    public function getStatistics(): ResultRow
    {
        if($this->statistics === null){
            $this->statistics = new ResultRow();
        }
        return $this->statistics;
    }

    /**
     * @param ResultRow $statistics
     */
    public function setStatistics(ResultRow $statistics): void
    {
        $this->statistics = $statistics;
    }

    /**
     * @return ResultRow|null
     */
    public function getStatisticsToday(): ?ResultRow
    {
        if ($this->statisticsToday === null) {
            $this->statisticsToday = new ResultRow();
        }
        return $this->statisticsToday;
    }

    /**
     * @param ResultRow|null $statisticsToday
     */
    public function setStatisticsToday(?ResultRow $statisticsToday): void
    {
        $this->statisticsToday = $statisticsToday;
    }


}
