<?php

namespace Enmash\Bundle\StoreBundle\Admin;

use Enmash\Bundle\StoreBundle\Entity\Store;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class StoreAdmin extends Admin
{

    public $last_position = 0;

    private $container;
    private $positionService;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setPositionService(\Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    );


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

        $this->last_position = $this
            ->positionService
            ->getLastPosition(
                $this->getRoot()->getClass()
            );

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
                    'move' => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
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
            ->add('whName')
            ->add('publish', null, array(
                    'required'  => false
                ))
            ->add('schedule')
            ->add('wholesaleSchedule', null, array(
                'required'      => false
            ))
            ->add('city')
            ->add('address')
            ->add('whAddress')
            ->add('latitude')
            ->add('longitude')
            ->add('info', 'ckeditor', array(
                    'config'    => array(
                        'toolbar'   => array(

                            array(
                                'items' => array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'Link', '-', 'RemoveFormat')
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
        if (!in_array(Store::WHOLESALE_TYPE, $object->getStoreType()) && !in_array(Store::ORDER_TYPE, $object->getStoreType())) {
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

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }
}
