<?php

namespace PassPlusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Catalog
 *
 * @ORM\Table(name="catalog", indexes={@ORM\Index(name="IDX_1B2C3247B5B63A6B", columns={"vat_id"})})
 * @ORM\Entity
 */
class Catalog
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="pretax_price", type="float", precision=10, scale=0, nullable=false)
     */
    private $pretaxPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activated", type="boolean", nullable=false)
     */
    private $activated;

    /**
     * @var \Vat
     *
     * @ORM\ManyToOne(targetEntity="Vat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vat_id", referencedColumnName="id")
     * })
     */
    private $vat;



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
     * Set name
     *
     * @param string $name
     *
     * @return Catalog
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Catalog
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set pretaxPrice
     *
     * @param float $pretaxPrice
     *
     * @return Catalog
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
     * Set activated
     *
     * @param boolean $activated
     *
     * @return Catalog
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set vat
     *
     * @param \PassPlusBundle\Entity\Vat $vat
     *
     * @return Catalog
     */
    public function setVat(\PassPlusBundle\Entity\Vat $vat = null)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return \PassPlusBundle\Entity\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }
}
