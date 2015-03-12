<?php

namespace AthenaPlus\SchooltripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entry
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AthenaPlus\SchooltripBundle\Entity\EntryRepository")
 */
class Entry
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
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var array
     *
     * @ORM\Column(name="items", type="json_array", nullable=true)
     */
    private $items;

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
     * @return Entry
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
     * Set user
     *
     * @param string $user
     * @return Entry
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Entry
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Set text
     *
     * @param string $text
     * @return Entry
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set items
     *
     * @param array $items
     * @return Entry
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return array 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="entries")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    protected $section;

    /**
     * Set section
     *
     * @param Section $section
     */
    public function setSection(Section $section = null)
    {
        $this->section = $section;
    }

    /**
     * Get section
     *
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
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


}
