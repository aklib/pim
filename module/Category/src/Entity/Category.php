<?php /** @noinspection PhpUnused */

namespace Category\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Category
 *
 * @ORM\NamedQueries({
 *   @ORM\NamedQuery(
 *      name = "attributes",
 *      query = "SELECT e FROM __CLASS__ e ORDER BY e.sortOrder ASC"
 *   )
 * })
 * @ORM\Table(name="category", indexes={@ORM\Index(name="FK_attribute_attribute_context", columns={"context_id"}), @ORM\Index(name="FK_attribute_attribute_tab", columns={"tab_id"}), @ORM\Index(name="FK_attribute_attribute_type", columns={"type_id"}), @ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="CategoryDao")
 */
class Category
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
     * @ORM\Column(name="unique_key", type="string", length=32, nullable=false)
     */
    private string $uniqueKey;

    /**
     *
     * @ORM\Column(name="label", type="string", length=64, nullable=false)
     */
    private string $label;


}
