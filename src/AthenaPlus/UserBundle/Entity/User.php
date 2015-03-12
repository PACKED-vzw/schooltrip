<?php
namespace AthenaPlus\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();

    }

    /**
     *
     * @ORM\ManyToOne(targetEntity="AthenaPlus\SchooltripBundle\Entity\ClassGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    protected $group;




    /**
     * @var string
     *
     * @ORM\Column(name="notification_settings", type="json_array", nullable=true)
     */
    private $notificationSettings;

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
     * Set notificationSettings
     *
     * @param string $notificationSettings
     * @return User
     */
    public function setNotificationSettings($notificationSettings)
    {
        $this->notificationSettings = $notificationSettings;

        return $this;
    }

    /**
     * Get notificationSettings
     *
     * @return string
     */
    public function getNotificationSettings()
    {
        return $this->notificationSettings;
    }


    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }

}
