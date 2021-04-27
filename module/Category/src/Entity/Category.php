<?php /** @noinspection TypoSafeNamingInspection */
/** @noinspection UnknownInspectionInspection */

/** @noinspection PhpUnused */

namespace Category\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Application\Doctrine\Annotation as AppORM;

/**
 * Category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *      name = "dropdownChoice",
 *      query = "SELECT e.id, e.name FROM __CLASS__ e"
 *   )
 * })
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Category\Repository\CategoryDao")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @AppORM\Column(sortOrder="1")
     */
    private int $id;

    /**
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     * @AppORM\Column(sortOrder="3")
     */
    private string $name;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     * @AppORM\Column(hidden=true)
     */
    private int $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     * @AppORM\Column(hidden=true)
     */
    private int $rgt;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @AppORM\Column(sortOrder="4")
     */
    private ?Category $parent;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category\Entity\Category")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     * @AppORM\Column(sortOrder="5")
     */
    private ?Category $root;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     * @AppORM\Column(sortOrder="6")
     */
    private int $level;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @AppORM\Column(hidden=true)
     */
    private Collection $children;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private ?DateTime $created;

    /**
     *
     * @ORM\Column(name="create_id", type="integer", nullable=true)
     */
    private ?int $createId;

    /**
     * @Gedmo\Timestampable(on="update", field={"key", "name"})
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     */
    private ?DateTime $changed;

    /**
     *
     * @ORM\Column(name="change_id", type="integer", nullable=true)
     */
    private ?int $changeId;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * @return int
     */
    public function getLft(): int
    {
        return $this->lft;
    }

    /**
     * @param int $lft
     */
    public function setLft(int $lft): void
    {
        $this->lft = $lft;
    }

    /**
     * @return int
     */
    public function getRgt(): int
    {
        return $this->rgt;
    }

    /**
     * @param int $rgt
     */
    public function setRgt(int $rgt): void
    {
        $this->rgt = $rgt;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     */
    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Category|null
     */
    public function getRoot(): ?Category
    {
        return $this->root;
    }

    /**
     * @param Category|null $root
     */
    public function setRoot(?Category $root): void
    {
        $this->root = $root;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection|Collection $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
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
}
