<?php

namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="config")
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="contact_email")
     */
    protected $contactEmail;

    /**
     * @ORM\Column(type="integer", name="blogs_limit")
     */
    protected $blogsLimit;

    /**
     * @ORM\Column(type="integer", name="comments_limit")
     */
    protected $commentsLimit;

    /**
     * @param mixed $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @param mixed $blogsLimit
     */
    public function setBlogsLimit($blogsLimit)
    {
        $this->blogsLimit = $blogsLimit;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @return mixed
     */
    public function getBlogsLimit()
    {
        return $this->blogsLimit;
    }

    /**
     * @return mixed
     */
    public function getCommentsLimit()
    {
        return $this->commentsLimit;
    }

    /**
     * @param mixed $commentsLimit
     */
    public function setCommentsLimit($commentsLimit)
    {
        $this->commentsLimit = $commentsLimit;
    }
}
