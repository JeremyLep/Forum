<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ThemesRepository extends EntityRepository
{
	public function getThemesLastDiscus($page, $nbPerPage) {
		
	    $cnx = $this->getEntityManager()->getConnection();
	    $query = $this->createQueryBuilder('t')

	      ->innerJoin('t.discussion', 'd')
	      ->addSelect('d')
	      ->innerJoin('d.auteur', 'm')
	      ->addSelect('m')
	      ->orderBy('t.id', 'ASC')
	      ->groupBy('t.id')
	      ->getQuery();

	    $query->setFirstResult(($page-1) * $nbPerPage)
      	  	  ->setMaxResults($nbPerPage);

    	return new Paginator($query, true);

	    /**

	    -- Plus efficace mais pas compatible avec la paginator de doctrine. --

	    $query = <<<SQL
        	SELECT themes.*, discussion.auteur_id, discussion.date, membres.pseudo
			FROM themes 
			INNER JOIN discussion
			ON discussion.id = (
				SELECT id FROM discussion 
				WHERE discussion.id = themes.id )
			INNER JOIN membres
			ON discussion.auteur_id = membres.id
			ORDER BY discussion.date DESC
SQL;
		return $cnx->fetchAll($query);*/

	}
}