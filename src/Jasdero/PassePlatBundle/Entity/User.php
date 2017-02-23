<?php

namespace Jasdero\PassePlatBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="user")
     */
    private $addresses;

    public function __construct()
    {
        parent::__construct();
        $this->addresses = new ArrayCollection();    }

    /**
     * Add address
     *
     * @param \Jasdero\PassePlatBundle\Entity\Address $address
     *
     * @return User
     */
    public function addAddress(\Jasdero\PassePlatBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \Jasdero\PassePlatBundle\Entity\Address $address
     */
    public function removeAddress(\Jasdero\PassePlatBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }
}
