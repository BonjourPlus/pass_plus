<?php

namespace PassPlusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="IDX_D34A04ADCFFE9AD6", columns={"orders_id"}), @ORM\Index(name="IDX_D34A04ADCC3C66FC", columns={"catalog_id"}), @ORM\Index(name="IDX_D34A04AD5D83CC1", columns={"state_id"})})
 * @ORM\Entity
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="pretax_price", type="float", precision=10, scale=0, nullable=false)
     */
    private $pretaxPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="vat_rate", type="float", precision=10, scale=0, nullable=false)
     */
    private $vatRate;

    /**
     * @var \State
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    private $state;

    /**
     * @var \Catalog
     *
     * @ORM\ManyToOne(targetEntity="Catalog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="catalog_id", referencedColumnName="id")
     * })
     */
    private $catalog;

    /**
     * @var \Orders
     *
     * @ORM\ManyToOne(targetEntity="Orders")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orders_id", referencedColumnName="id")
     * })
     */
    private $orders;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pretaxPrice
     *
     * @param float $pretaxPrice
     *
     * @return Product
     */
    public function setPretaxPrice($pretaxPrice)
    {
        $this->pretaxPrice = $pretaxPrice;

        return $this;
    }

    /**
     * Get pretaxPrice
     *
     * @return float
     */
    public function getPretaxPrice()
    {
        return $this->pretaxPrice;
    }

    /**
     * Set vatRate
     *
     * @param float $vatRate
     *
     * @return Product
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate
     *
     * @return float
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set state
     *
     * @param \PassPlusBundle\Entity\State $state
     *
     * @return Product
     */
    public function setState(\PassPlusBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \PassPlusBundle\Entity\State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set catalog
     *
     * @param \PassPlusBundle\Entity\Catalog $catalog
     *
     * @return Product
     */
    public function setCatalog(\PassPlusBundle\Entity\Catalog $catalog = null)
    {
        $this->catalog = $catalog;

        return $this;
    }

    /**
     * Get catalog
     *
     * @return \PassPlusBundle\Entity\Catalog
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * Set orders
     *
     * @param \PassPlusBundle\Entity\Orders $orders
     *
     * @return Product
     */
    public function setOrders(\PassPlusBundle\Entity\Orders $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \PassPlusBundle\Entity\Orders
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
