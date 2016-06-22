<?php

namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Blogger\BlogBundle\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        $this->apiKey = md5(uniqid());
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="api_key")
     */
    protected $apiKey;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profilePicturePath;

    /**
     * @Assert\File(maxSize="2048k")
     * @Assert\Image(mimeTypesMessage="Please upload a valid image.")
     */
    protected $profilePictureFile;

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return array
     */
    public function getRole()
    {
        if (!empty($this->roles)) {
            return $this->roles;
        }

        return ['ROLE_USER'];
    }

    /**
     * @return string
     */
    public function getUserRoles()
    {
        if (!empty($this->roles)) {
            return implode(', ', $this->roles);
        }

        return 'ROLE_USER';
    }

    /**
     * @param $role
     */
    public function setRole($role)
    {
        $this->setRoles(array($role));
    }

    /**
     * @return mixed
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * @param mixed $profilePicturePath
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;
    }

    /**
     * @return mixed
     */
    public function getProfilePictureFile()
    {
        return $this->profilePictureFile;
    }

    /**
     * @param mixed $profilePictureFile
     */
    public function setProfilePictureFile($profilePictureFile)
    {
        $this->profilePictureFile = $profilePictureFile;
    }

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        if (empty($this->profilePictureFile)) {
            return;
        }

        if (is_null($this->profilePicturePath)) {
            $imageName = md5(uniqid()) . $this->profilePictureFile->getClientOriginalName();
            $this->setProfilePicturePath($imageName);
            $imageDir = __DIR__ . '/../../../../web/images/profilePicture';
            $this->profilePictureFile->move($imageDir, $imageName);
        }
    }


}
