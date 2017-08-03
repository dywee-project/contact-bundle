<?php

namespace Dywee\ContactBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    public function countNew()
    {
        $qb = $this->createQueryBuilder('m')
        ->select('COUNT(m)')
        ->where('m.status = 0');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findForDropdown($website)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->orderBy('m.sentAt', 'desc')
            ->where('m.status = 0')
        ;

        return $qb->getQuery()->getResult();
    }
}
