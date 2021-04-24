<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProductStatisticsPublisher
 *
 * @ORM\Table(name="product_statistics_publisher", uniqueConstraints={@ORM\UniqueConstraint(name="publisher_id_campaign_id_date", columns={"publisher_id", "campaign_id", "date"})}, indexes={@ORM\Index(name="FK_product_statistics_publisher_product", columns={"campaign_id"}), @ORM\Index(name="IDX_7C294C6640C86FCE", columns={"publisher_id"})})
 * @ORM\Entity
 */
class ProductStatisticsPublisher
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="requests", type="integer", nullable=false, options={"comment"="count all requests"})
     */
    private $requests;

    /**
     * @var int
     *
     * @ORM\Column(name="bids", type="integer", nullable=false, options={"comment"="count all bids"})
     */
    private $bids;

    /**
     * @var int
     *
     * @ORM\Column(name="bids_sell", type="integer", nullable=false, options={"comment"="count bids won"})
     */
    private $bidsSell;

    /**
     * @var float
     *
     * @ORM\Column(name="revenue", type="float", precision=10, scale=0, nullable=false)
     */
    private $revenue;

    /**
     * @var \CompanyPublisher
     *
     * @ORM\ManyToOne(targetEntity="CompanyPublisher")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publisher_id", referencedColumnName="id")
     * })
     */
    private $publisher;

    /**
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     * })
     */
    private $campaign;


}
