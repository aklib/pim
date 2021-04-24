<?php /** @noinspection PhpUnused */

/**
 *
 * Class UserRole
 *
 * @since 19.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Acl\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use User\Entity\UserRole;

/**
 * AclResource
 *
 * @ORM\Table(name="acl_resource", uniqueConstraints={@ORM\UniqueConstraint(name="type_resource_privilege", columns={"type", "resource", "privilege"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity(repositoryClass="Acl\Repository\AclResourceDao")
 */
class AclResource
{

    public const RESOURCE_TYPE_MVC = 'mvc';
    public const RESOURCE_TYPE_COLUMN = 'column';
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
     * @ORM\Column(name="type", type="string", length=8, nullable=false)
     */
    private string $type;

    /**
     * @var string
     *
     * @ORM\Column(name="resource", type="string", length=256, nullable=false)
     */
    private string $resource;

    /**
     * @var string
     *
     * @ORM\Column(name="privilege", type="string", length=32, nullable=false)
     */
    private string $privilege;

    /**
     * @var int|null
     *
     * @ORM\Column(name="create_id", type="integer", nullable=true)
     */
    private int $createId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private ?DateTime $created;

    /**
     * @var int|null
     *
     * @ORM\Column(name="change_id", type="integer", nullable=true)
     */
    private int $changeId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     */
    private ?DateTime $changed;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\UserRole", mappedBy="resources")
     * @ORM\JoinTable(name="acl_resource_role_xref")
     */
    private Collection $userRoles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @param string $resource
     */
    public function setResource(string $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getPrivilege(): string
    {
        return $this->privilege;
    }

    /**
     * @param string $privilege
     */
    public function setPrivilege(string $privilege): void
    {
        $this->privilege = $privilege;
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
     * @return Collection
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @param Collection $userRoles
     */
    public function setUserRoles(Collection $userRoles): void
    {
        $this->userRoles = $userRoles;
    }

    // ======================= METHODS REQUIRED FOR HYDRATION =======================

    public function addUserRole(UserRole $userRole): void
    {
        $this->userRoles->add($userRole);
    }

    public function removeUserRole(UserRole $userRole): void
    {
        $this->userRoles->removeElement($userRole);
    }
}
