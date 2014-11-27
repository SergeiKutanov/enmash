<?php

namespace Enmash\Bundle\PagesBundle\Admin;

use Enmash\Bundle\PagesBundle\Entity\Article;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ArticleAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('abstract')
            ->add('body')
            ->add('publish')
            ->add('createdAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('featured')
            ->add('title')
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('createdAt', 'sonata_type_date_picker')
            ->add('publish')
            ->add('featured')
            ->add('title')
            ->add('featuredImage',
                'sonata_media_type',
                array(
                    'provider' => 'sonata.media.provider.image',
                    'context'  => 'articleimages',
                    'required' => false
                )
            )
            ->add(
                'abstract',
                'ckeditor',
                array(
                    'config'    => array(
                        'toolbar'   => array(

                            array(
                                'items' => array(
                                    'Source',
                                    '-',
                                    'FontSize',
                                    'TextColor',
                                    '-',
                                    'JustifyLeft',
                                    'JustifyCenter',
                                    'JustifyRight',
                                    'JustifyBlock',
                                    '-',
                                    'Bold',
                                    'Italic',
                                    'Underline',
                                    'Strike',
                                    'Subscript',
                                    'Superscript',
                                    '-',
                                    'NumberedList',
                                    'BulletedList',
                                    '-',
                                    'RemoveFormat',
                                    '-',
                                    'Table',
                                    'HorizontalRule'
                                )
                            )
                        )
                    )
                )
            )
            ->add(
                'body',
                'ckeditor',
                array(
                    'config'    => array(
                        'toolbar'   => array(
                            array(
                                'items' => array(
                                    'Source',
                                    '-',
                                    'FontSize',
                                    'TextColor',
                                    '-',
                                    'JustifyLeft',
                                    'JustifyCenter',
                                    'JustifyRight',
                                    'JustifyBlock',
                                    '-',
                                    'Bold',
                                    'Italic',
                                    'Underline',
                                    'Strike',
                                    'Subscript',
                                    'Superscript',
                                    '-',
                                    'NumberedList',
                                    'BulletedList',
                                    '-',
                                    'RemoveFormat',
                                    '-',
                                    'Table',
                                    'HorizontalRule'
                                )
                            ),
                            array(
                                'Image'
                            )
                        )
                    )
                )
            )
            ->add(
                'products',
                'sonata_type_model_autocomplete',
                array(
                    'property'      => 'name',
                    'placeholder'   => 'Название товара',
                    'multiple'      => true,
                    'required'      => false
                )
            )
            ->add(
                'connectedArticles'
//                'sonata_type_model_list',
//                array(
//                    'property'      => 'title',
//                    'placeholder'   => 'Заголовок статьи',
//                    'multiple'      => true,
//                    'required'      => false
//                )
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('abstract')
            ->add('body')
            ->add('publish')
            ->add('createdAt')
        ;
    }

    public function prePersist($object)
    {
        $this->fixRelations($object);
    }

    public function preUpdate($object)
    {
        /* @var $object Article */
        $this->fixRelations($object);
    }

    protected function fixRelations(Article $object) {

        //todo a stupid way to delete connected entitites. Got to find a better way
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $articles = $em->getRepository('EnmashPagesBundle:Article')->findAll();
        foreach ($articles as $article) {
            /* @var $article Article */
            if (!is_null($article->getParentArticle())) {
                if ($article->getParentArticle()->getId() == $object->getId()) {
                    if (!$object->getConnectedArticles()->contains($article)) {
                        $article->setParentArticle(null);
                        $em->persist($article);
                    }
                }
            }
        }
        $em->flush();

        if (is_null($object->getConnectedArticles())) {
            return;
        }
        
        foreach ($object->getConnectedArticles() as $article) {
            /* @var $article \Enmash\Bundle\PagesBundle\Entity\Article */
            $article->setParentArticle($object);
        }
    }
}
