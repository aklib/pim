<?php /** @noinspection PhpUnused */

namespace Attribute\Entity;

use Application\Entity\AppCountry;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeValueCountry
 *
 * @ORM\Table(name="attribute_value_country", uniqueConstraints={@ORM\UniqueConstraint(name="value_id_val", columns={"value_id", "val"})}, indexes={@ORM\Index(name="FK_attribute_value_country_app_country", columns={"val"}), @ORM\Index(name="FK_attribute_value_country_attribute_value", columns={"value_id"})})
 * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeValueCountryDao")
 */
class AttributeValueCountry
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
     * @ORM\ManyToOne(targetEntity="Application\Entity\AppCountry", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="val", referencedColumnName="id", nullable=false)
     * })
     */
    private AppCountry $val;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Attribute\Entity\AttributeValue", inversedBy="valueCountries", cascade={"persist"})
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
     * @return AppCountry
     */
    public function getVal(): AppCountry
    {
        return $this->val;
    }

    /**
     * @param AppCountry $val
     */
    public function setVal(AppCountry $val): void
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
        return $this->getVal()->getCode();
    }
}
