<?php
/**
 * Class Bid
 * @package Report\Entity
 *
 * since: 28.09.2020
 * author: alexej@kisselev.de
 */

namespace Report\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered Advertiser company.
 * @ORM\NamedQueries({
 *      @ORM\NamedQuery(
 *      name = "attributes",
 *      query = "SELECT e FROM Attribute\Entity\Attribute e INNER JOIN e.context c WHERE c.context = 'none' ORDER BY e.sortOrder ASC"
 *   )
 * })
 *
 * @ORM\Entity(repositoryClass="Report\Repository\BidDao", readOnly=true)
 */
class Bid
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
     * @var Error
     *
     * @ORM\OneToOne (targetEntity="Report\Entity\Error")
     */
    private Error $error;

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
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getTotalHits(): int
    {
        return $this->totalHits;
    }

    /**
     * @param int $totalHits
     */
    public function setTotalHits(int $totalHits): void
    {
        $this->totalHits = $totalHits;
    }

    /**
     * @return Device
     */
    public function getDevice(): Device
    {
        return $this->device;
    }

    /**
     * @param Device $device
     */
    public function setDevice(Device $device): void
    {
        $this->device = $device;
    }

    /**
     * @return Macros
     */
    public function getMacros(): Macros
    {
        return $this->macros;
    }

    /**
     * @param Macros $macros
     */
    public function setMacros(Macros $macros): void
    {
        $this->macros = $macros;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     */
    public function setCampaign(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    /**
     * @return Advertiser
     */
    public function getAdvertiser(): Advertiser
    {
        return $this->advertiser;
    }

    /**
     * @param Advertiser $advertiser
     */
    public function setAdvertiser(Advertiser $advertiser): void
    {
        $this->advertiser = $advertiser;
    }

    /**
     * @return Publisher
     */
    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    /**
     * @param Publisher $publisher
     */
    public function setPublisher(Publisher $publisher): void
    {
        $this->publisher = $publisher;
    }

    /**
     * @return Error
     */
    public function getError(): Error
    {
        return $this->error;
    }

    /**
     * @param Error $error
     */
    public function setError(Error $error): void
    {
        $this->error = $error;
    }
}