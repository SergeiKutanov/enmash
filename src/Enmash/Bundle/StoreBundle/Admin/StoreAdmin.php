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
            ->add('city')
            ->add('address')
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
                    'choices'   => Store::getstoreTypeList(),
                    'multiple'  => true
                ))
            ->add('name')
            ->add('publish', null, array(
                    'required'  => false
                ))
            ->add('schedule')
            ->add('city')
            ->add('address')
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
            ->add('whInfo', 'ckeditor', array(
                    'required'  => false,
                    'config'    => array(
                        'toolbar'   => array(

                            array(
                                'items' => array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat')
                            )
                        )
                    )
                ))
            ->add(
                'contacts',
                'sonata_type_collection',
                array(
                    'required'  => false
                ),
                array(
                    'edit'  => 'inline',
                    'inline'=> 'table'
                )
            )
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
            ->add('latitude')
            ->add('longitude')
        ;
    }

    public function prePersist($object) {
        $this->fixRelations($object);
        $this->clearInfos($object);
    }

    public function preUpdate($object) {
        $this->fixRelations($object);
        $this->clearInfos($object);
    }

    protected function fixRelations($object) {
        /* @var $object Store */
        if (!is_null($object->getContacts())) {
            foreach ($object->getContacts() as $contact) {
                /* @var $contact \Enmash\Bundle\StoreBundle\Entity\StoreContact */
                $contact->setStore($object);
            }
        }
    }

    protected function clearInfos($object) {
        /* @var $object Store */
        if (!in_array(Store::WHOLESALE_TYPE, $object->getStoreType())) {
            $object->setWhInfo(null);
        }

        if (!in_array(Store::RETAIL_TYPE, $object->getStoreType()) && !in_array(Store::ORDER_TYPE, $object->getStoreType())) {
            $object->setInfo(null);
        }
    }

    public function getTemplate($name)
    {
        if ($name == 'edit') {
            return 'EnmashStoreBundle:Admin:storeedit.html.twig';
        }
        return parent::getTemplate($name);
    }
}
