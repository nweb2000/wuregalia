<?php
/* ---------------------------------------------
    User Entity class
    Holds data about user that is persited into 
    database via doctrine

    Author: Noah Weber
------------------------------------------------ */
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 *@ORM\Table(name="User")
 */
class User 
{
    /**
     *@ORM\Column(type="integer", nullable=false)
     *@ORM\Id
     *@ORM\GeneratedValue
     */
    protected $id;

    /**
    * @ORM\OneToMany(targetEntity="Reservation", mappedBy="userid")
    */
    protected $reservations; //reservations made by the user

    /**
     *@ORM\Column(type="string", nullable=false, length=255)
     */
    protected $fname;

    /**
     *@ORM\Column(type="string", nullable=false, length=255)
     */
    protected $lname;

    /**
     *@ORM\Column(type="string", nullable=false, unique=true, length=255)
     */
    protected $email;

    /**
     *@ORM\Column(type="string", nullable=false, unique=true, length=255)
    */
    protected $username;

    /**
     *@ORM\Column(type="datetime", nullable=false)
     */
    protected $signup_date; //when the user first signs into the system
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->signup_date = new \DateTime(); //this defualts to using creation date as default
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
     * Set fname
     *
     * @param string $fname
     * @return User
     */
    public function setFname($fname)
    {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get fname
     *
     * @return string 
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set lname
     *
     * @param string $lname
     * @return User
     */
    public function setLname($lname)
    {
        $this->lname = $lname;

        return $this;
    }

    /**
     * Get lname
     *
     * @return string 
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set signup_date
     *
     * @param \DateTime $signupDate
     * @return User
     */
    public function setSignupDate($signupDate)
    {
        $this->signup_date = $signupDate;

        return $this;
    }

    /**
     * Get signup_date
     *
     * @return \DateTime 
     */
    public function getSignupDate()
    {
        return $this->signup_date;
    }

    /**
     * Add reservations
     *
     * @param \AppBundle\Entity\Reservation $reservations
     * @return User
     */
    public function addReservation(\AppBundle\Entity\Reservation $reservations)
    {
        $this->reservations[] = $reservations;

        return $this;
    }

    /**
     * Remove reservations
     *
     * @param \AppBundle\Entity\Reservation $reservations
     */
    public function removeReservation(\AppBundle\Entity\Reservation $reservations)
    {
        $this->reservations->removeElement($reservations);
    }

    /**
     * Get reservations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }
}
