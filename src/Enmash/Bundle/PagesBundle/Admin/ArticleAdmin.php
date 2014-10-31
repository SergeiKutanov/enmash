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
            ->add(
                'abstract',
                'ckeditor',
                array(
                    'config'    => array(
                        'toolbar'   => array(

                            array(
                                'items' => array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')
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
                                    'RemoveFormat')
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
        $this->fixRelations($object);
    }

    protected function fixRelations(Article $object) {
        foreach ($object->getConnectedArticles() as $article) {
            /* @var $article \Enmash\Bundle\PagesBundle\Entity\Article */
            $article->setParentArticle($object);
        }
    }
}
