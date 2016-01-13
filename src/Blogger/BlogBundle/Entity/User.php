<?php

namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Uecode\Bundle\ApiKeyBundle\Model\ApiKeyUser;

/**
 * @ORM\Entity()
 * @ORM\Table(name="fos_user")
 */
class User extends ApiKeyUser
{
    public function __construct()
    {
        parent::__construct();
        $this->setEnabled(true);
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
}
