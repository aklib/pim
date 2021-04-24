<?php

namespace Product\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductType
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *     name = "dropdownChoice",
 *     query = "SELECT e.id, e.name FROM __CLASS__ e WHERE e.status = 1 ORDER BY e.name, e.sortOrder ASC"
 *   )
 * })
 *
 * @ORM\Table(name="product_type")
 * @ORM\Entity(repositoryClass="Product\Repository\ProductTypeDao")
 */
class ProductType
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private string $name;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private int $sortOrder;

    /**
     * @var int
     *
     * @ORM\Column(name="status_id", type="integer", nullable=false)
     */
    private int $status;

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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
