<?php /** @noinspection PhpUnused */

namespace Product\Entity;

use Attribute\Entity\AttributeValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeValue
 *
 * @ORM\Table(name="attribute_value")
 * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeValueDao")
 */
class ProductAttributeValue extends AttributeValue
{

    /**
     *
     * @ORM\ManyToOne(targetEntity="Product\Entity\Product", inversedBy="attributeValues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reference_id", referencedColumnName="id", nullable=false)
     * })
     */
    private Product $reference;

    /**
     * @return Product
     */
    public function getReference(): Product
    {
        return $this->reference;
    }

    /**
     * @param Product $reference
     */
    public function setReference(Product $reference): void
    {
        $this->reference = $reference;
    }



}
