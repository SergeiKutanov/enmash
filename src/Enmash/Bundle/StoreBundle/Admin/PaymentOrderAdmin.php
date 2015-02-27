<?php

namespace Enmash\Bundle\StoreBundle\Admin;

use Enmash\Bundle\StoreBundle\Entity\PaymentOrder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PaymentOrderAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('confirmed')
            ->add('updated')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'confirmed',
                null,
                array(
                    'editable'  => true,
                    'label'     => 'Оформлен'
                )
            )
            ->add(
                'updated',
                'datetime',
                array(
                    'label'     => 'Создан'
                )
            )
            ->add(
                'status',
                'string',
                array(
                    'label'     => 'Статус',
                    'template'  => 'EnmashStoreBundle:Admin:payment_order_list.html.twig'
                )
            )
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
            ->add('confirmed')
            ->add('updated', 'sonata_type_datetime_picker')
            ->add(
                'status',
                'choice',
                array(
                    'choices' => PaymentOrder::getChoiceList()
                )
            )
            ->add('comments')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {

        $showMapper
            ->add('confirmed', 'boolean')
            ->add('status')
            ->add('updated')
            ->add('user')
            ->add('products')
            ->add('comments')
        ;
    }
}
