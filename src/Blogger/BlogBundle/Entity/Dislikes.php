<?php

namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dislikes")
 * @ORM\Entity(repositoryClass="Blogger\BlogBundle\Entity\Repository\DislikesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Dislikes
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Blog", inversedBy="dislike")
     * @ORM\JoinColumn(name="blog_id", referencedColumnName="id")
     */
    protected $blog;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $userId;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Blogger\BlogBundle\Entity\Blog $blog
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @param \Blogger\BlogBundle\Entity\Blog $blog
     */
    public function setBlog(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}