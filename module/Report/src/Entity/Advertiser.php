<?php
/**
 * Class Advertiser
 * @package Report\Entity
 *
 * since: 28.09.2020
 * author: alexej@kisselev.de
 */

namespace Report\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a bid Advertiser.
 *
 * @ORM\Entity(repositoryClass="Report\Repository\AdvertiserDao", readOnly=true)
 */
class Advertiser
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