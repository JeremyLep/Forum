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
     * @var integer
     *
     * @ORM\Column(name="nb_discussion", type="integer", length=700, nullable=true)
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
}