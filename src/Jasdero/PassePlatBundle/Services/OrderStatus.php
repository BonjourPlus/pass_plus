<?php

namespace Jasdero\PassePlatBundle\Services;

use Jasdero\PassePlatBundle\Entity\Orders;
use Doctrine\ORM\EntityManager;

class OrderStatus
{

    /**
     * OrderStatus constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }

    /**
     * determine status of order according to products statuses
     * @param Orders $order
     */
    public function orderStatusAction(Orders $order)
    {


        $maxWeight = $this->em->getRepository('JasderoPassePlatBundle:Orders')->findMaxWeightInOrder($order);

        //Finding corresponding status
        $status = $this->em->getRepository('JasderoPassePlatBundle:State')->findOneBy(['weight' => $maxWeight]);

        //Setting order status
        $order->setState($status);
        $this->em->persist($order);
        $this->em->flush();

    }
}
