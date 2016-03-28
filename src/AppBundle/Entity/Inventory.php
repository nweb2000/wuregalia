<?php
/* ---------------------------------------------
    Inventory Entity class
    Holds data about an inventory item that is persisted
    to database via doctrine
    
    Author: Noah Weber
------------------------------------------------ */
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 *@ORM\Table(name="Inventory")
 */
class Inventory
{
    /**
     *@ORM\Column(type="integer", nullable=false)
     *@ORM\Id
     *@ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="inventoryItems")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=false)
     */
    protected $itemStatus; //ie is the item checkout, pending, etc

    /**
     * @ORM\ManyToOne(targetEntity="ItemType", inversedBy="inventoryItems")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    protected $itemType; //is it a cap, gown, etc

    /**
     * @ORM\ManyToOne(targetEntity="Color", inversedBy="inventoryItems")
     * @ORM\JoinColumn(name="color_id", referencedColumnName="id", nullable=false)
     */
    protected $itemColor;

    /**
     * @ORM\ManyToOne(targetEntity="School", inversedBy="inventoryItems")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id", nullable=false)
     */
    protected $itemSchool; //where the item is from

    /**
     * @ORM\ManyToOne(targetEntity="Major", inversedBy="inventoryItems")
     * @ORM\JoinColumn(name="major_id", referencedColumnName="id", nullable=false)
     */
    protected $itemMajor; //what discipline the item is associated with

    /**
     *@ORM\Column(type="string", nullable=false, length=500)
     */
    protected $itemDescription; 

    /**
     *@ORM\Column(type="integer")
     */
    protected $itemLength;

    /**
     *@ORM\Column(type="integer")
     */
    protected $itemWidth;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="inventoryItems")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

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
     * Set itemDescription
     *
     * @param string $itemDescription
     * @return Inventory
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;

        return $this;
    }

    /**
     * Get itemDescription
     *
     * @return string 
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * Set itemLength
     *
     * @param integer $itemLength
     * @return Inventory
     */
    public function setItemLength($itemLength)
    {
        $this->itemLength = $itemLength;

        return $this;
    }

    /**
     * Get itemLength
     *
     * @return integer 
     */
    public function getItemLength()
    {
        return $this->itemLength;
    }

    /**
     * Set itemWidth
     *
     * @param integer $itemWidth
     * @return Inventory
     */
    public function setItemWidth($itemWidth)
    {
        $this->itemWidth = $itemWidth;

        return $this;
    }

    /**
     * Get itemWidth
     *
     * @return integer 
     */
    public function getItemWidth()
    {
        return $this->itemWidth;
    }

    /**
     * Set itemStatus
     *
     * @param \AppBundle\Entity\Status $itemStatus
     * @return Inventory
     */
    public function setItemStatus(\AppBundle\Entity\Status $itemStatus)
    {
        $this->itemStatus = $itemStatus;

        return $this;
    }

    /**
     * Get itemStatus
     *
     * @return \AppBundle\Entity\Status 
     */
    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    /**
     * Set itemType
     *
     * @param \AppBundle\Entity\ItemType $itemType
     * @return Inventory
     */
    public function setItemType(\AppBundle\Entity\ItemType $itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return \AppBundle\Entity\ItemType 
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set itemColor
     *
     * @param \AppBundle\Entity\Color $itemColor
     * @return Inventory
     */
    public function setItemColor(\AppBundle\Entity\Color $itemColor)
    {
        $this->itemColor = $itemColor;

        return $this;
    }

    /**
     * Get itemColor
     *
     * @return \AppBundle\Entity\Color 
     */
    public function getItemColor()
    {
        return $this->itemColor;
    }

    /**
     * Set itemSchool
     *
     * @param \AppBundle\Entity\School $itemSchool
     * @return Inventory
     */
    public function setItemSchool(\AppBundle\Entity\School $itemSchool)
    {
        $this->itemSchool = $itemSchool;

        return $this;
    }

    /**
     * Get itemSchool
     *
     * @return \AppBundle\Entity\School 
     */
    public function getItemSchool()
    {
        return $this->itemSchool;
    }

    /**
     * Set itemMajor
     *
     * @param \AppBundle\Entity\Major $itemMajor
     * @return Inventory
     */
    public function setItemMajor(\AppBundle\Entity\Major $itemMajor)
    {
        $this->itemMajor = $itemMajor;

        return $this;
    }

    /**
     * Get itemMajor
     *
     * @return \AppBundle\Entity\Major 
     */
    public function getItemMajor()
    {
        return $this->itemMajor;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Inventory
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

}
