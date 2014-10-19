<?php

namespace Enmash\Bundle\StoreBundle\Admin;

use Enmash\Bundle\StoreBundle\Entity\Store;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class StoreAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('id')
//            ->add('name')
            ->add('address')
//            ->add('contact')
//            ->add('latitude')
//            ->add('longitude')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->addIdentifier('name')
            ->add('address')
            ->add('contact')
//            ->add('latitude')
//            ->add('longitude')
            ->add('_action', 'actions', array(
                'actions' => array(
//                    'show' => array(),
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
            ->add('storeType', 'choice', array(
                    'choices'   => Store::getstoreTypeList()
                ))
            ->add('name')
            ->add('publish', null, array(
                    'required'  => false
                ))
            ->add('schedule')
            ->add('address')
            ->add('contact')
            ->add('latitude')
            ->add('longitude')
            ->add('info', 'ckeditor', array(
                    'config'    => array(
                        'toolbar'   => array(

                            array(
                                'items' => array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')
                            )
                        )
                    )
                ))
            ->add(
                'storeImages',
                'sonata_type_model_list',
                array(
                    'required'  => false
                ),
                array(
                    'link_parameters'   => array(
                        'context'   => 'storeimage'
                    )
                )
            )
//            ->add(
//                'storeImages'
//                'sonata_type_collection'
//            )
//
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
            ->add('address')
            ->add('contact')
            ->add('latitude')
            ->add('longitude')
        ;
    }

}
