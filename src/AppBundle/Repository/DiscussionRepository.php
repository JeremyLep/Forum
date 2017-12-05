<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DiscussionRepository extends EntityRepository
{
	public function getDiscussionTheme() {

	}

	public function getNbDiscussion() {
	    $cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT COUNT(id)
			FROM discussion
SQL;
		return $stmt = $cnx->fetchColumn($query);
	}
}