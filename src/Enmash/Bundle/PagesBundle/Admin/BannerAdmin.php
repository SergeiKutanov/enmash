<?php

namespace Enmash\Bundle\PagesBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BannerAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('content')
            ->add('isPublished')
            ->add('startDate')
            ->add('endDate')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('photo', null, array(
                    'template'  => 'SonataAdminBundle::imagelistview.html.twig'
                ))
            ->add('name')
            ->add('isPublished')
            ->add('startDate', 'date')
            ->add('endDate', 'date')
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
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('content', 'ckeditor', array(
                    'config'    => array(
                        'toolbar'   => array(

                            array(
                                'items' => array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')
                            )
                        )
                    )
                ))
            ->add('isPublished')
            ->add('startDate', 'sonata_type_date_picker')
            ->add('endDate', 'sonata_type_date_picker')
            ->add('photo',
                'sonata_media_type',
                array(
                    'provider' => 'sonata.media.provider.image',
                    'context'  => 'banner',
                    'required' => false
                )
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
            ->add('name')
            ->add('content')
            ->add('isPublished')
            ->add('startDate')
            ->add('endDate')
        ;
    }
}
