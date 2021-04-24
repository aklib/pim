<?php


use Doctrine\ORM\Mapping as ORM;

/**
 * ProductStatisticsAdvertiser
 *
 * @ORM\Table(name="product_statistics_advertiser", uniqueConstraints={@ORM\UniqueConstraint(name="advertiser_id_campaign_id_date", columns={"advertiser_id", "campaign_id", "date"})}, indexes={@ORM\Index(name="FK_product_statistics_product", columns={"campaign_id"}), @ORM\Index(name="IDX_A8BFC623BA2FCBC2", columns={"advertiser_id"})})
 * @ORM\Entity
 */
class ProductStatisticsAdvertiser
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
     * @ORM\Column(name="bids", type="integer", nullable=false, options={"comment"="count all bids"})
     */
    private $bids;

    /**
     * @var int
     *
     * @ORM\Column(name="bid_won", type="integer", nullable=false, options={"comment"="count bids won"})
     */
    private $bidWon;

    /**
     * @var int
     *
     * @ORM\Column(name="bid_clicked", type="integer", nullable=false, options={"comment"="count bids clicked"})
     */
    private $bidClicked;

    /**
     * @var int
     *
     * @ORM\Column(name="ip", type="integer", nullable=false, options={"comment"="count unique IPs"})
     */
    private $ip;

    /**
     * @var \CompanyAdvertiser
     *
     * @ORM\ManyToOne(targetEntity="CompanyAdvertiser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="advertiser_id", referencedColumnName="id")
     * })
     */
    private $advertiser;

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
