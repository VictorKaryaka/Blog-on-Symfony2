<?php

namespace Blogger\BlogBundle\Twig\Extensions;

class BloggerBlogExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return ['created_ago' => new \Twig_Filter_Method($this, 'createdAgo')];
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public function createdAgo(\DateTime $dateTime)
    {
        $delta = time() - $dateTime->getTimestamp();

        if ($delta < 0) {
            throw new \InvalidArgumentException("createdAgo is unable to handle dates in the future");
        }

        if ($delta < 60) {
            $time = $delta;
            $duration = $time . " second" . (($time === 0 || $time > 1) ? "s" : "") . " ago";
        } else if ($delta < 3600) {
            $time = floor($delta / 60);
            $duration = $time . " minute" . (($time > 1) ? "s" : "") . " ago";
        } else if ($delta < 86400) {
            $time = floor($delta / 3600);
            $duration = $time . " hour" . (($time > 1) ? "s" : "") . " ago";
        } else {
            $time = floor($delta / 86400);
            $duration = $time . " day" . (($time > 1) ? "s" : "") . " ago";
        }

        return $duration;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blogger_blog_extension';
    }
}