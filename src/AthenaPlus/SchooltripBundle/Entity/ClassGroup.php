<?php

namespace AthenaPlus\SchooltripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ClassGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AthenaPlus\SchooltripBundle\Entity\ClassGroupRepository")
 */
class ClassGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @ORM\OneToMany(targetEntity="AthenaPlus\UserBundle\Entity\User", mappedBy="group")
     */
    private $users;


    /**
     * @ORM\ManyToOne(targetEntity="Trip", inversedBy="groups")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     */
    protected $trip;

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
     * Set name
     *
     * @param string $name
     * @return ClassGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add user
     *
     * @param AthenaPlus\UserBundle\Entity\User $user
     */
    public function addUser(\AthenaPlus\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;
    }

    /**
     * Set user
     *
     * @param Doctrine\Common\Collections\Collection $users
     */
    public function setUsers($users)
    {

        foreach($users as $user){

            $this->users[] = $user;

        }

    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get trip
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTrip()
    {
        return $this->trip;
    }

    /**
     * Set trip
     *
     *
     */
    public function setTrip($trip)
    {
        $this->trip = $trip;
    }

    public function resetTrip()
    {
        unset($this->trip);
    }

}
