<?php


namespace Jasdero\PassePlatBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function findOrderByCatalog($id)
    {
        $dq = $this->createQueryBuilder('p');
        $dq ->select('orders.id')
            ->join('p.orders', 'orders')
            ->join('p.catalog', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->groupBy('orders.id');

        return $dq->getQuery()->getResult();
    }

}