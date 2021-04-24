<?php
/**
 * Class Publisher
 * @package Report\Entity
 *
 * since: 28.09.2020
 * author: alexej@kisselev.de
 */

namespace Report\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a a bid Publisher.
 *
 * @ORM\Entity(repositoryClass="Report\Repository\PublisherDao", readOnly=true)
 */
class Publisher
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
}