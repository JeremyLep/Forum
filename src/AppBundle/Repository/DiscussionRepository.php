<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DiscussionRepository extends EntityRepository
{
	public function getDiscussions($theme, $page, $nbPerPage) {

		$query = $this->createQueryBuilder('d')
	      ->innerJoin('d.theme', 't')
	      ->where('t.titre = :theme')
	      ->addSelect('t')
	      ->innerJoin('d.auteur', 'm')
	      ->addSelect('m')
	      ->orderBy('d.date', 'ASC')
	      ->setParameter('theme', $theme)
	      ->getQuery();

    	$query->setFirstResult(($page-1) * $nbPerPage)
      	  	  ->setMaxResults($nbPerPage);

    	return new Paginator($query, true);
	}

	public function getinfoTheme($page, $nbPerPage) {
		$cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT max(d.date) AS date, m.username, d.theme_id
			FROM discussion AS d, user AS m
			WHERE d.auteur_id = m.id
			GROUP BY d.theme_id
SQL;
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

	public function getStatProfil($id) {

		$cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT COUNT(d.id), COUNT(distinct d.theme_id)
			FROM discussion d
			WHERE d.auteur_id = ':id'
SQL;
		$query = str_replace(':id', $id, $query);
		return $cnx->fetchAll($query);
	}

	public function getStatSite() {

		$cnx = $this->getEntityManager()->getConnection();
	    $query = <<<SQL
        	SELECT COUNT(d.id), COUNT(distinct d.theme_id)
			FROM discussion d
SQL;
		return $cnx->fetchAll($query);
	}
}