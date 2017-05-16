<?php


namespace Jasdero\PassePlatBundle\Repository;


use Doctrine\ORM\EntityRepository;

class StateRepository extends EntityRepository
{
    public function findAllStatesWithAssociations()
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->leftJoin('s.orders', 'o')
            ->addSelect('o')
            ->where('o.archive = false')
            ->leftJoin('s.products', 'p')
            ->addSelect('p')
            ->orderBy('s.weight', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findBottomState()
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->select('s')
            ->where('s.activated = true')
            ->orderBy('s.weight', 'ASC')
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }
}
