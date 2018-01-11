<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getActive()
    {
        $delay = new \DateTime();
        $delay->setTimestamp(strtotime('2 minutes ago'));
 
        $qb = $this->createQueryBuilder('u')
                   ->where('u.lastActivity > :delay')
                   ->setParameter('delay', $delay);
 
        return $qb->getQuery()->getResult();
    }
}