<?php /** @noinspection PhpUnused */

namespace User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonException;

/**
 * UserConfig
 *
 * @ORM\Table(name="user_config", uniqueConstraints={@ORM\UniqueConstraint(name="user_id_name_resource", columns={"user_id", "name", "resource"})}, indexes={@ORM\Index(name="IDX_B1D83441A76ED395", columns={"user_id"}), @ORM\Index(name="resource", columns={"resource"})})
 * @ORM\Entity(repositoryClass="User\Repository\UserConfigDao")
 */
class UserConfig
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
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private string $name;

    /**
     *
     * @ORM\Column(name="resource", type="string", length=255, nullable=false)
     */
    private string $resource;

    /**
     *
     * @ORM\Column(name="current", type="boolean", nullable=false)
     */
    private bool $current;

    /**
     *
     * @ORM\Column(name="columns", type="json", length=65535, nullable=true)
     */
    private string $columns;

    /**
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private DateTime $created;

    /**
     *
     * @ORM\Column(name="create_id", type="integer", nullable=true)
     */
    private int $createId;

    /**
     *
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     */
    private DateTime $changed;

    /**
     *
     * @ORM\Column(name="change_id", type="integer", nullable=true)
     */
    private int $changeId;
    /**
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="configs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private User $user;

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
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @param bool $current
     */
    public function setCurrent(bool $current): void
    {
        $this->current = $current;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        if (is_string($this->columns)) {
            try {
                return json_decode($this->columns, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
            }
        }
        return $this->columns;
    }

    /**
     * @param string|array $columns
     */
    public function setColumns($columns): void
    {
        $this->columns = is_array($columns) ? json_encode($columns, JSON_THROW_ON_ERROR) : $columns;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getCreateId(): int
    {
        return $this->createId;
    }

    /**
     * @param int $createId
     */
    public function setCreateId(int $createId): void
    {
        $this->createId = $createId;
    }

    /**
     * @return DateTime
     */
    public function getChanged(): DateTime
    {
        return $this->changed;
    }

    /**
     * @param DateTime $changed
     */
    public function setChanged(DateTime $changed): void
    {
        $this->changed = $changed;
    }

    /**
     * @return int
     */
    public function getChangeId(): int
    {
        return $this->changeId;
    }

    /**
     * @param int $changeId
     */
    public function setChangeId(int $changeId): void
    {
        $this->changeId = $changeId;
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

}
