<?php

namespace AthenaPlus\SchooltripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trip
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AthenaPlus\SchooltripBundle\Entity\TripRepository")
 */
class Trip
{
    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="AthenaPlus\SchooltripBundle\Entity\ClassGroup", mappedBy="trip")
     */
    private $groups;

    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=255)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="departure", type="string", length=255)
     */
    private $departure;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


    /**
     * @var date
     *
     * @ORM\Column(name="date_from", type="date", nullable=true)
     */
    private $dateFrom;

    /**
     * @var date
     *
     * @ORM\Column(name="date_to", type="date", nullable=true)
     */
    private $dateTo;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state;


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
     * Set title
     *
     * @param string $title
     * @return Trip
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return Trip
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }


    /**
     * Set departure
     *
     * @param string $departure
     * @return Trip
     */
    public function setDeparture($departure)
    {
        $this->departure = $departure;

        return $this;
    }

    /**
     * Get departure
     *
     * @return string
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * Set groups
     *
     * @param Doctrine\Common\Collections\Collection $groups
     */
    public function setGroups($groups)
    {

        foreach($groups as $group){

            $this->groups[] = $group;

        }

    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }


    /**
     * Set description
     *
     * @param string $description
     * @return Trip
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set  state
     *
     * @param string $state
     * @return Trip
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }




    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="trip", cascade={"remove"})
     */
    protected $sections;

    /**
     * Add section
     *
     * @param Section $section
     */
    public function addSection(Section $section)
    {
        $this->sections[] = $section;
    }

    /**
     * Get sections
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @ORM\OneToOne(targetEntity="Journal", mappedBy="trip", cascade={"remove"})
     */
    protected $journal;

    /**
     * Add journal
     *
     * @param Journal $journal
     */
    public function addJournal(Journal $journal)
    {

        $this->journals = $journal;
    }

    /**
     * Get journal
     *
     * @return Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }


    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     */
    public function setDateFrom(\DateTime $dateFrom)
    {
        // don't change if there is already a journal created
        if(!$this->journal){
            $this->dateFrom = $dateFrom;
        }
        return $this;

    }


    /**
     * Get dateFrom
     *
     * @return
     */
    public function getDateFrom($format = "object")
    {
        if(!$this->dateFrom){
            $this->dateFrom = new \DateTime();
        }
        switch ($format) {
            case 'string':
                return $this->dateFrom->format('d/m/Y');
                break;
            case 'object':
            default:
                return $this->dateFrom;
        }

    }


    /**
     * Set dateTo
     *
     * @param \DateTime  $dateTo
     */
    public function setDateTo(\DateTime $dateTo)
    {
        // don't change if there is already a journal created
        if(!$this->journal){
            $this->dateTo = $dateTo;
        }
    }

    /**
     * Get dateTo
     *
     * @return date
     */
    public function getDateTo($format = "object")
    {
        if(!$this->dateTo){
            $this->dateTo = new \DateTime();
        }
        switch ($format) {
            case 'string':
                return $this->dateTo->format('d/m/Y');
                break;
            case 'object':
            default:
                return $this->dateTo;
        }
    }



}
