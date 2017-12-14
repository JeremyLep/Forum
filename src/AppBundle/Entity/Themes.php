<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\UniqueEntity;

/**
 * Themes
 *
 * @ORM\Table(name="themes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemesRepository")
 * @UniqueEntity(fields="title", message="Un thème existe déjà avec ce titre.")
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
     * @ORM\Column(name="titre", type="string", length=200, nullable=false, unique=true)
     * @Assert/Length(min=3,max=200)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="sous_titre", type="string", length=500, nullable=true)
     * @Assert/Length(min=3,max=500)
     */
    private $sousTitre;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_discussion", type="integer", length=700, nullable=true)
     * @Assert/Type(int)
     */
    private $nbDiscussion;

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

    /**
     * Add discussion
     *
     * @param \AppBundle\Entity\Discussion $discussion
     *
     * @return Fiche
     */
    public function addDiscussion(\AppBundle\Entity\Discussion $discussion)
    {
        $this->discussion[] = $discussion;
        $this->nbDiscussion++;

        return $this;
    }

    /**
     * Remove discussion
     *
     * @param \AppBundle\Entity\Discussion $discussion
     */
    public function removeDiscussion(\AppBundle\Entity\Discussion $discussion)
    {
        $this->discussion->removeElement($discussion);
        $this->nbDiscussion--;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Themes
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set sousTitre
     *
     * @param string $sousTitre
     *
     * @return Themes
     */
    public function setSousTitre($sousTitre)
    {
        $this->sousTitre = $sousTitre;

        return $this;
    }

    /**
     * Get sousTitre
     *
     * @return string
     */
    public function getSousTitre()
    {
        return $this->sousTitre;
    }

    /**
     * Set nbDiscussion
     *
     * @param integer $nbDiscussion
     *
     * @return Themes
     */
    public function setNbDiscussion($nbDiscussion)
    {
        $this->nbDiscussion = $nbDiscussion;

        return $this;
    }

    /**
     * Get nbDiscussion
     *
     * @return integer
     */
    public function getNbDiscussion()
    {
        return $this->nbDiscussion;
    }

    /**
     * Get discussion
     *
     * @return \Discussion
     */
    public function getDiscussion()
    {
        return $this->discussion;
    }
}
