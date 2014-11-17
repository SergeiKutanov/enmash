<?php

namespace Enmash\Bundle\StoreBundle\Admin;

use Doctrine\ORM\UnitOfWork;
use Enmash\Bundle\StoreBundle\Entity\Category;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Enmash\Bundle\StoreBundle\Entity\ProductParameter;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProductAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('sku')
            ->add('acronym')
            ->add('mansku')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('sku')
            ->add('acronym')
            ->add('mansku')
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
            ->add(
                'manufacturer',
                'sonata_type_model_list'
            )
            ->add(
                'category',
                'sonata_type_model_list'
            )
            ->add('sku')
            ->add('mansku')
            ->add('name')
            ->add('acronym')
            ->add(
                'parameters',
                'sonata_type_collection',
                array(
                    'btn_add'       => false,
                    'required'      => false,
                    'type_options'  => array(
                        'delete'    => false
                    )
                ),
                array(
                    'edit'      => 'inline',
                    'inline'    => 'table',
                    'delete'    => false
                )
            )
            ->add(
                'productImages',
                'sonata_type_model_list',
                array(
                    'required'  => false,
                    'by_reference' => false
                ),
                array(
                    'link_parameters'   => array(
                        'context'   => 'productimage'
                    )
                )
            )
            ->add(
                'analogs',
                null,
                array(
                    'required'  => false,
                    'by_reference' => false
                )
            )
            ->add(
                'similars',
                null,
                array(
                    'required'  => false,
                    'by_reference' => false
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
            ->add('sku')
            ->add('acronym')
            ->add('mansku')
        ;
    }

    public function prePersist($object) {

        $category = $object->getCategory();

        $parameters = $category->getParameters();

        foreach ($parameters as $parameter) {
            $productParameter = new ProductParameter();
            $productParameter->setCategoryParameter($parameter);
            $object->addParameter($productParameter);

        }

        $this->fixRelations($object);
    }

    public function preUpdate($object) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $original = $em->getUnitOfWork()->getOriginalEntityData($object);

        /* @var $object Product */
        /* @var $category Category */
        $category = $object->getCategory();

        if ($category->getId() != $original['category_id']) {
            $parameters = $category->getParameters();

            foreach ($object->getParameters() as $parameter) {
                $object->removeParameter($parameter);
            }

            foreach ($parameters as $parameter) {
                $productParameter = new ProductParameter();
                $productParameter->setCategoryParameter($parameter);
                $object->addParameter($productParameter);

            }
        }

        $this->fixRelations($object);
    }


    private function fixRelations($object) {
        /* @var $object \Enmash\Bundle\StoreBundle\Entity\Product */
        foreach ($object->getParameters() as $parameter) {
            /* @var $parameter \Enmash\Bundle\StoreBundle\Entity\ProductParameter */
            $parameter->setProduct($object);
        }
    }

}
