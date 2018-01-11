<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FosUser;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends FosUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", nullable=true)
     * @Assert\Length(min=2)
     */
    public $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", nullable=true)
     * @Assert\Length(min=2)
     */
    protected $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", nullable=true)
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "La taille maximal d'un avatar est de 5MB.",
     *     mimeTypesMessage = "Seulement des fichier de type image sont autorisé (jpg, png, tiff, gif)"
     * )
     */
    protected $avatar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetime", nullable=false)
     * @Assert\DateTime()
     */
    protected $dateInscription;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     * @Assert\Range(min=5, max=99)
     */
    protected $age;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", nullable=true)
     * @Assert\Regex(pattern="(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}", message="Le numero de téléphone doit etre au format français.")
     */
    protected $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", nullable=true)
     * @Assert\Length(min=3)
     */
    protected $ville;

    /**
     * @CaptchaAssert\ValidCaptcha(
     *      message = "Le contrôle de sécurité n'a pas été validé. Veuillez réessayer."
     * )
     */
    protected $captchaCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valid", type="boolean", nullable=false)
     */
    protected $valid;

    /**
     * @var \Discussion
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Discussion", mappedBy="auteur")
     *
     */
    protected $discussion;

    /**
     * @var datetime
     *
     * @ORM\Column(name="last_activity", type="datetime", nullable=true)
    */
    protected $lastActivity; 

    public function __construct()
    {
        $this->dateInscription = new \DateTime;
        $this->valid = false;
        $this->roles = array('ROLE_USER');
        parent::__construct();
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
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     *
     * @return User
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Set lastActivity
     *
     * @return datetime
     */
    public function setLastActivity()
    {
        $this->lastActivity = new \DateTime();

        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return string
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }


    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return User
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }

    /**
     * Set valid
     *
     * @param boolean $valid
     *
     * @return User
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return boolean
     */
    public function getValid()
    {
        return $this->valid;
    }

    public function isAuthor(User $user = null)
    {
        return $user && $user->getEmail() == $this->getEmail();
    }
}
