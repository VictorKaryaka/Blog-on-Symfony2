<?php

namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Blogger\BlogBundle\Entity\Repository\BlogRepository")
 * @ORM\Table(name="blog")
 * @ORM\HasLifecycleCallbacks
 *
 * @ExclusionPolicy("all")
 */
class Blog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Expose
     */
    protected $title;

    /**
     * @ORM\Column(type="simple_array")
     * @Expose
     */
    protected $author;

    /**
     * @ORM\Column(type="text")
     * @Expose
     */
    protected $blog;

    /**
     * @ORM\Column(type="text")
     * @Expose
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="blog", cascade={"remove"})
     */
    protected $comments;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     */
    protected $updated;

    /**
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="blog", cascade={"persist", "remove"})
     * @Expose
     */
    protected $image;

    /**
     * @ORM\OneToMany(targetEntity="Likes", mappedBy="blog", cascade={"persist", "remove"})
     * @Expose
     */
    protected $like;

    /**
     * @ORM\OneToMany(targetEntity="Dislikes", mappedBy="blog", cascade={"persist", "remove"})
     * @Expose
     */
    protected $dislike;

    /**
     * @var ArrayCollection
     */
    private $uploadedFiles;

    /**
     * @return ArrayCollection
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @param ArrayCollection $uploadedFiles
     */
    public function setUploadedFiles($uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * Blog constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->image = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
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
     *
     * @return Blog
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->setSlug($this->title);
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
     * Set author
     *
     * @param array $author
     *
     * @return Blog
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return array
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set blog
     *
     * @param string $blog
     *
     * @return Blog
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * Get blog
     *
     * @return string
     */
    public function getBlog($length = null)
    {
        if (false === is_null($length) && $length > 0) {
            return substr($this->blog, 0, $length);
        } else {
            return $this->blog;
        }
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return Blog
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Blog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Blog
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     * @param mixed $like
     */
    public function setLike($like)
    {
        $this->like = $like;
    }

    /**
     * @return mixed
     */
    public function getDislike()
    {
        return $this->dislike;
    }

    /**
     * @param mixed $dislike
     */
    public function setDislike($dislike)
    {
        $this->dislike = $dislike;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdatedValue()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * Remove comment
     *
     * @param \Blogger\BlogBundle\Entity\Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Blog
     */
    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function slugify($text)
    {
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);
        $text = trim($text, '-');

        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        $text = strtolower($text);
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Blog
     */
    public function addImage(Image $image)
    {
        $this->image[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Blogger\BlogBundle\Entity\Image $image
     */
    public function removeImage(Image $image)
    {
        $this->image->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        if (!$this->uploadedFiles == null) {
            foreach ($this->uploadedFiles as $uploadedFile) {

                if (is_object($uploadedFile)) {
                    $image = new Image();
                    $imageName = md5(uniqid()) . $uploadedFile->getClientOriginalName();
                    $image->setName($imageName);
                    $image->setMain(0);
                    $imageDir = __DIR__ . '/../../../../web/images';
                    $uploadedFile->move($imageDir, $imageName);
                    $this->getImage()->add($image);
                    $image->setBlog($this);

                    unset($uploadedFile);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getAuthors() {
        return implode(',', $this->author);
    }

    /**
     * Add like
     *
     * @param Blog $like
     *
     * @return Blog
     */
    public function addLike(Blog $like)
    {
        $this->like[] = $like;

        return $this;
    }

    /**
     * Remove like
     *
     * @param Blog $like
     */
    public function removeLike(Blog $like)
    {
        $this->like->removeElement($like);
    }

    /**
     * Add dislike
     *
     * @param Blog $dislike
     *
     * @return Blog
     */
    public function addDislike(Blog $dislike)
    {
        $this->dislike[] = $dislike;

        return $this;
    }

    /**
     * Remove dislike
     *
     * @param Blog $dislike
     */
    public function removeDislike(Blog $dislike)
    {
        $this->dislike->removeElement($dislike);
    }
}
