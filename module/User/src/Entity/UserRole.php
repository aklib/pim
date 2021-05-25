<?php /** @noinspection PhpUnused */

/**
 *
 * UserRole.php
 *
 * @since 19.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace User\Entity;

use Acl\Entity\AclResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserRole
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *      name = "dropdownChoice",
 *      query = "SELECT e.id, e.name FROM __CLASS__ e WHERE e.priority <= :priority ORDER BY e.name ASC"
 *   )
 * })
 * @ORM\Table(name="user_role", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="User\Repository\UserRoleDao")
 */
class UserRole
{

    public const USER_ROLE_DEVELOPER = 'developer';
    public const USER_ROLE_ADMIN = 'admin';
    public const USER_ROLE_REDACTOR = 'redactor';
    public const USER_ROLE_MANAGER = 'manager';
    public const USER_ROLE_ADVERTISER = 'advertiser';
    public const USER_ROLE_PUBLISHER = 'publisher';

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
     * @ORM\Column(name="unique_key", type="string", length=16, nullable=false)
     */
    private string $uniqueKey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private string $name;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private int $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="restricted", type="integer", nullable=false)
     */
    private int $restricted;

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
    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }

    /**
     * @param string $uniqueKey
     */
    public function setUniqueKey(string $uniqueKey): void
    {
        $this->uniqueKey = $uniqueKey;
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
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return bool
     */
    public function isRestricted(): bool
    {
        return $this->restricted;
    }

    /**
     * @param bool $restricted
     */
    public function setRestricted(bool $restricted): void
    {
        $this->restricted = $restricted;
    }
}
