<?php

namespace AthenaPlus\SchooltripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Section
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AthenaPlus\SchooltripBundle\Entity\SectionRepository")
 */
class Section
{

    public function __construct()
    {
        $this->entries = new ArrayCollection();
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
     * @ORM\Column(name="ready", type="boolean", nullable=true)
     */
    private $ready;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=true)
     */
    private $parameters;

    /**
     * @var array
     *
     * @ORM\Column(name="media", type="json_array", nullable=true)
     */
    private $media;

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
     * @return Section
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
     * Set type
     *
     * @param string $type
     * @return Section
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set description
     *
     * @param string $description
     * @return Section
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
     * Set parameters
     *
     * @param string $parameters
     * @return Section
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Trip", inversedBy="sections")
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
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="section", cascade={"remove"})
     */
    protected $entries;

    /**
     * Add entry
     *
     * @param Entry $entry
     */
    public function addEntry(Entry $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * Get entries
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Get media
     *
     * @return array
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Get single media object
     *
     * @return array
     */
    public function getMediaObject($id)
    {
        $media = $this->media;
        for($i = 0; $i < count($media); $i++){
            if(!(isset($media[$i]))){
                continue;
            }
            if($media[$i]['id']==$id){
                return $media[$i];
            }
        }
    }

    /**
     * Update single media object
     *
     * @return array
     */
    public function updateMediaObject($id, $description, $label)
    {
        $media = $this->media;

        for($i = 0; $i < count($media); $i++){
            if(!(isset($media[$i]))){
                continue;
            }
            if($media[$i]['id']==$id){
                $media[$i]['description'] = $description;
                $media[$i]['label']       = $label;
            }
        }

        return $this->media = $media;
    }

    /**
     * Set media
     *
     * @param array $media
     * @return Section
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Add media
     *
     * @param string $media
     * @return Section
     */
    public function addMedia($media)
    {
        $this->media[] = $media;

        return $this;
    }

    /**
     * remove media
     *
     * @param string $mediaId
     * @return Section
     */
    public function removeMedia($mediaId)
    {
        $media = $this->media;
        for($i = 0; $i < count($media); $i++){
            if(!(isset($media[$i]))){
                continue;
            }
            if($media[$i]['id']==$mediaId){
                unset($media[$i]);
                break;
            }
        }
        $this->media = $media;
        return $this->media;
    }

    /**
     * Set ready
     *
     * @param string $ready
     * @return InformationUnit
     */
    public function setReady($ready)
    {
        $this->ready = $ready;

        return $this;
    }


    /**
     * Get ready
     *
     * @return string
     */
    public function getReady()
    {

        return $this->ready;
    }



}
