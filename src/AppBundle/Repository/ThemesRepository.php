<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ThemesRepository extends EntityRepository
{
	public function getThemesLastDiscus() {
	    $cnx = $this->getEntityManager()->getConnection();
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
		return $stmt = $cnx->fetchAll($query);
	}
}