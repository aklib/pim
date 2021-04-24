<?php
/**
 * Class UserAttributeValue
 * @package User\Entity
 *
 * since: 20.08.2020
 * author: alexej@kisselev.de
 */

namespace User\Entity;

use Attribute\Entity\AttributeValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeValue
 *
 * @ORM\Table(name="attribute_value", indexes={@ORM\Index(name="FK_attribute_value_user", columns={"reference_id"})})
 * @ORM\Entity(repositoryClass="Attribute\Repository\AttributeValueDao")
 */
class UserAttributeValue extends AttributeValue
{
    /**
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="attributeValues")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reference_id", referencedColumnName="id", nullable=false)
     * })
     */
    private User $reference;

    /**
     * @return User
     */
    public function getReference(): User
    {
        return $this->reference;
    }

    /**
     * @param User $reference
     */
    public function setReference(User $reference): void
    {
        $this->reference = $reference;
    }

}