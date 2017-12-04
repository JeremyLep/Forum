<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discussion
 *
 * @ORM\Table(name="discussion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiscussionRepository")
 */
class Discussion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=5000, nullable=false)
     */
    private $contenu;

    /**
     * @var \Membres
     *
     * @ORM\ManyToOne(targetEntity="Membres", inversedBy="id")
     *
     */
    private $auteur;


    /**
     * @var \Themes
     *
     * @ORM\ManyToOne(targetEntity="Themes", inversedBy="discussion")
     *
     */
    private $theme;
}