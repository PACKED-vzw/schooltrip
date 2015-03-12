<?php

namespace AthenaPlus\SchooltripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Journal
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AthenaPlus\SchooltripBundle\Entity\JournalRepository")
 */
class Journal
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=true, options={"default" : 0})
     */
    private $published;

    /**
     * @var string
     *
     * @ORM\Column(name="dates", type="json_array")
     */
    private $dates;

    /**
     * @var string
     *
     * @ORM\Column(name="journal_dates", type="json_array", nullable=true)
     */
    private $journalDates;


    /**
     * @var string
     *
     * @ORM\Column(name="evaluation_request", type="json_array", nullable=true)
     */
    private $evaluationRequest;


    /**
     * @ORM\OneToOne(targetEntity="Trip", inversedBy="journal")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     */
    protected $trip;

    /**
     * Set trip
     *
     * @param Trip $trip
     */
    public function setTrip(Trip $trip = null)
    {
        $this->trip = $trip;
    }

    /**
     * Get trip
     *
     * @return Trip
     */
    public function getTrip()
    {
        return $this->trip;
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
     * Set title
     *
     * @param string $title
     * @return Journal
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
     * Set published
     *
     * @param boolean $published
     * @return Journal
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }


    /**
     * @ORM\OneToMany(targetEntity="Tab", mappedBy="journal", cascade={"remove"})
     */
    protected $tabs;

    /**
     * Add tab
     *
     * @param Tab $tab
     */
    public function addTab(Tab $tab)
    {
        $this->tabs[] = $tab;
    }

    /**
     * Get tabs
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * Set dates
     *
     * @param string $dates
     * @return Journal
     */
    public function setDates($dates)
    {
        $this->dates = $dates;

        return $this;
    }

    /**
     * Get dates
     *
     * @return string
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Set evaluationRequest
     *
     * @param string $evaluationRequest
     * @return Journal
     */
    public function setEvaluationRequest($evaluationRequest)
    {
        $this->evaluationRequest = $evaluationRequest;

        return $this;
    }

    /**
     * Get evaluationRequest
     *
     * @return string
     */
    public function getEvaluationRequest()
    {
        return $this->evaluationRequest;
    }


    /**
     * Set journalDates
     *
     * @param string $journalDates
     * @return Journal
     */
    public function setJournalDates($journalDates)
    {
        $this->journalDates = $journalDates;

        return $this;
    }

    /**
     * Get dates
     *
     * @return string
     */
    public function getJournalDates()
    {
        return $this->journalDates;
    }

}
