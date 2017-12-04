<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * Themes
 *
 * @ORM\Table(name="themes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemesRepository")
 */
class Themes
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
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=300, nullable=false)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="sous_titre", type="string", length=700, nullable=true)
     */
    private $sousTitre;

    /**
     * @var \Discussion
     *
     * @ORM\OneToMany(targetEntity="Discussion", mappedBy="theme")
     *
     */
    private $discussion;

    public function __construct()
    {
        $this->discussion = new ArrayCollection();
    }
}