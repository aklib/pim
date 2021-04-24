<?php /** @noinspection PhpUnused */

/**
 *
 * Country.php
 *
 * @since  03.08.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *     name = "dropdownChoice",
 *     query = "SELECT e.id, e.name FROM __CLASS__ e ORDER BY e.name, e.sortOrder ASC"
 *   ),
 *      @ORM\NamedQuery(
 *      name = "dropdownChoiceByCode2",
 *      query = "SELECT e.code as id, e.name FROM __CLASS__ e ORDER BY e.name, e.sortOrder ASC"
 *   )
 * })
 *
 * @ORM\Table(name="app_country", indexes={@ORM\Index(name="idx_be2e492b77153098", columns={"code"}), @ORM\Index(name="idx_be2e492b937180dd", columns={"code3"})})
 * @ORM\Entity(repositoryClass="Application\Repository\CountryDao")
 */
class AppCountry
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=2, nullable=false)
     */
    private string $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code3", type="string", length=3, nullable=false)
     */
    private string $code3;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private int $sortOrder;

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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode3(): string
    {
        return $this->code3;
    }

    /**
     * @param string $code3
     */
    public function setCode3(string $code3): void
    {
        $this->code3 = $code3;
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

    public function __toString()
    {
        return $this->getCode();
    }
}
