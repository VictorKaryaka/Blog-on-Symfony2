<?php

namespace Blogger\BlogBundle\Admin;

use Blogger\BlogBundle\Entity\Comment;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CommentAdmin extends Admin
{
    protected $entityManager;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param EntityManager $entityManager
     */
    public function __construct($code, $class, $baseControllerName, EntityManager $entityManager)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $commentList = [];
        $blog = $this->getRequest()->request->get($this->getUniqid())['blog'];

        if ($blog) {
            $comments = $this->entityManager->getRepository('BloggerBlogBundle:Comment')->getCommentsForBlog($blog);
        } else {
            $comments = $this->entityManager->getRepository('BloggerBlogBundle:Comment')->getCommentsForFirstBlog();
        }

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                if (is_object($comment)) {
                    $commentList[$comment->getId()] = $comment->getComment();
                } else {
                    $commentList[$comment['id']] = $comment['comment'];
                }
            }
        }

        $formMapper->add('user', HiddenType::class, [
            'data' => $this->getConfigurationPool()
                ->getContainer()
                ->get('security.token_storage')
                ->getToken()
                ->getUser()
                ->getUsername()
        ])
            ->add('comment', 'textarea')
            ->add('blog', 'entity', [
                'class' => 'Blogger\BlogBundle\Entity\Blog',
                'property' => 'title',
            ])
            ->add('parentId', 'choice', [
                'required' => false,
                'choices' => $commentList,
            ])
            ->add('created', 'datetime')
            ->add('updated', 'datetime');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('blog', null, ['label' => 'blog ID'])
            ->add('user')
            ->add('comment')
            ->add('parentId')
            ->add('created')
            ->add('updated')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('user')
            ->add('created')
            ->add('updated');
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('user')
            ->add('comment')
            ->add('parentId')
            ->add('created')
            ->add('updated');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('getComments', $this->getRouterIdParameter() . '/getComments');
    }

    /**
     * @param mixed $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof Comment ? $object->getComment() : 'Comment';
    }
}