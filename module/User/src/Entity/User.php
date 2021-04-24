<?php /** @noinspection TypoSafeNamingInspection */

/** @noinspection PhpUnused */

namespace User\Entity;

use Application\Entity\AbstractAttributeEntity;
use Application\Entity\AppStatus;
use Attribute\Entity\AttributeValue;
use Company\Entity\Advertiser;
use Company\Entity\Publisher;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * This class represents a registered user.
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *      name = "dropdownChoice",
 *      query = "SELECT e.id, e.firstName, e.lastName FROM __CLASS__ e"
 *   ),
 *   @ORM\NamedQuery(
 *      name = "attributeColumns",
 *      query = "SELECT e.id, e.name, e.sortOrder, e.label, t.type FROM Attribute\Entity\Attribute e INNER JOIN e.context c INNER JOIN e.type t WHERE c.context='user' ORDER BY e.sortOrder ASC"
 *   ),
 *      @ORM\NamedQuery(
 *      name = "attributes",
 *      query = "SELECT e FROM Attribute\Entity\Attribute e INNER JOIN e.context c WHERE c.context='user' ORDER BY e.sortOrder ASC"
 *   )
 * })
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="IDX_8D93D6496BF700BD", columns={"status_id"}), @ORM\Index(name="IDX_8D93D649D60322AC", columns={"role_id"})})
 * @ORM\Entity(repositoryClass="User\Repository\UserDao")
 */
class User
{
    // User status constants.
    public const STATUS_ACTIVE = 1;   // Active user.
    public const STATUS_NEW = 2;      // New user.
    public const STATUS_INACTIVE = 3; // Retired/Disabled user.

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Application\Entity\Generator\SequenceGenerator")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private string $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=64, nullable=false)
     */
    private string $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=64, nullable=true)
     */
    private ?string $lastName;

    /**
     * @var AppStatus
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\AppStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    private AppStatus $status;
    /**
     * @var string|null
     *
     * @ORM\Column(name="pwd_reset_token", type="string", length=64, nullable=true)
     */
    private ?string $passwordResetToken;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="pwd_reset_token_creation_date", type="datetime", nullable=true)
     */
    private ?DateTime $passwordResetTokenCreationDate;

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
     * @var UserRole
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\UserRole")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     * })
     */
    private UserRole $userRole;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="User\Entity\UserConfig", mappedBy="user")
     */
    private $configs;

    public function __construct()
    {
        $this->configs = new ArrayCollection();
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    /**
     * @param string|null $passwordResetToken
     */
    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    /**
     * @return string|null
     */
    public function getPasswordResetTokenCreationDate(): ?DateTime
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * @param DateTime|null $passwordResetTokenCreationDate
     */
    public function setPasswordResetTokenCreationDate(?DateTime $passwordResetTokenCreationDate): void
    {
        $this->passwordResetTokenCreationDate = $passwordResetTokenCreationDate;
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
     * @return UserRole
     */
    public function getUserRole(): UserRole
    {
        return $this->userRole;
    }

    /**
     * @param UserRole $userRole
     */
    public function setUserRole(UserRole $userRole): void
    {
        $this->userRole = $userRole;
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
     * @return Collection
     */
    public function getAttributeValues(): Collection
    {
        return $this->attributeValues;
    }

    /**
     * @param Collection $attributeValues
     */
    public function setAttributeValues(Collection $attributeValues): void
    {
        /** @var AttributeValue $attributeValue */
        foreach ($attributeValues as $attributeValue) {
            $this->addAttributeValue($attributeValue);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getConfigs(): Collection
    {
        return $this->configs;
    }

    /**
     * @param Collection $configs
     */
    public function setConfigs(Collection $configs): void
    {
        $this->configs = $configs;
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
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function removeAttributeValue(AttributeValue $attributeValue): void
    {
        $attributeValue->setReference(null);
        $this->attributeValues->removeElement($attributeValue);
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function addAttributeValue(AttributeValue $attributeValue): void
    {
        $attributeValue->setReferenceId($this->getId());
        $this->attributeValues->add($attributeValue);
    }

    /**
     * @param Collection $configs
     */
    public function removeUserConfigs(Collection $configs): void
    {
        /** @var UserConfig $config */
        foreach ($configs as $config) {
            $this->removeUserConfig($config);
        }
    }

    /**
     * @param UserConfig $config
     */
    public function removeUserConfig(UserConfig $config): void
    {
        $this->configs->removeElement($config);
    }

    /**
     * @param Collection $configs
     */
    public function addUserConfigs(Collection $configs): void
    {
        /** @var UserConfig $config */
        foreach ($configs as $config) {
            $this->addUserConfig($config);
        }
    }

    /**
     * @param UserConfig $config
     */
    public function addUserConfig(UserConfig $config): void
    {
        $config->setUser($this);
        $this->configs->add($config);
    }


    /**
     * @param Collection $publishers
     */
    public function removePublishers(Collection $publishers): void
    {
        /** @var Publisher $publisher */
        foreach ($publishers as $publisher) {
            $this->removePublisher($publisher);
        }
    }

    /**
     * @param Publisher $publisher
     */
    public function removePublisher(Publisher $publisher): void
    {
        $this->publishers->removeElement($publisher);
    }

    /**
     * @param Collection $publishers
     */
    public function addPublishers(Collection $publishers): void
    {
        /** @var Publisher $publisher */
        foreach ($publishers as $publisher) {
            $this->addPublisher($publisher);
        }
    }

    /**
     * @param Publisher $publisher
     */
    public function addPublisher(Publisher $publisher): void
    {
        $this->publishers->add($publisher);
        if (!$publisher->getUsers()->contains($this)) {
            $publisher->addUser($this);
        }
    }


    /**
     * @param Collection $advertisers
     */
    public function removeAdvertisers(Collection $advertisers): void
    {
        /** @var Publisher $publisher */
        foreach ($advertisers as $publisher) {
            $this->removeAdvertiser($publisher);
        }
    }

    /**
     * @param Advertiser $advertiser
     */
    public function removeAdvertiser(Advertiser $advertiser): void
    {
        $this->advertisers->removeElement($advertiser);
    }

    /**
     * @param Collection $advertisers
     */
    public function addAdvertisers(Collection $advertisers): void
    {
        /** @var Advertiser $advertiser */
        foreach ($advertisers as $advertiser) {
            $this->addAdvertiser($advertiser);
        }
    }

    /**
     * @param Advertiser $advertiser
     */
    public function addAdvertiser(Advertiser $advertiser): void
    {
        $this->advertisers->add($advertiser);
        if (!$advertiser->getUsers()->contains($this)) {
            $advertiser->addUser($this);
        }
    }
}
