<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DiscussionRepository extends EntityRepository
{
	public function getDiscussions($theme) {
		$cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT d.*, m.pseudo
			FROM discussion d
			JOIN themes t
			JOIN membres m
			WHERE t.titre = ":theme"
			AND d.theme_id = t.id
			AND m.id = d.auteur_id
			ORDER BY d.date ASC
SQL;
		$query = str_replace(":theme", (string)$theme, $query);

		return $cnx->fetchAll($query);
	}

	public function getNbDiscussion() {
	    $cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT COUNT(id)
			FROM discussion
SQL;
		return $cnx->fetchColumn($query);
	}

	public function getInfoCountDiscussions($theme) {
	    $cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT t.nb_discussion, COUNT(distinct d.auteur_id)
			FROM discussion d
			JOIN themes t
			WHERE t.titre = ':theme'
			AND t.id = d.theme_id
SQL;
		$query = str_replace(':theme', (string)$theme, $query);
		return $cnx->fetchAll($query);
	}
}