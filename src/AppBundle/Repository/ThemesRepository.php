<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\Expr;

class ThemesRepository extends EntityRepository
{
	public function getThemes($page, $nbPerPage) {
		
	    $cnx = $this->getEntityManager()->getConnection();
	    $query = $this->createQueryBuilder('t')
	      ->addOrderBy('t.id', 'ASC')
	      ->getQuery();

	    $query->setFirstResult(($page-1) * $nbPerPage)
      	  	  ->setMaxResults($nbPerPage);

    	return new Paginator($query, true);
	}

}