<?php


namespace Jasdero\PassePlatBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OrdersRepository extends EntityRepository
{
    public function countOrders()
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('count(o.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}