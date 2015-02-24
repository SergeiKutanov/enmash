<?php

namespace Enmash\Bundle\StoreBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class CategoryAdmin extends Admin
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
            ->add('id')
            ->add('name')
            ->add('parentCategory')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->last_position = $this->positionService->getLastPosition($this->getRoot()->getClass());

        $listMapper
            ->add('name')
            ->add('parentCategory')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'move' => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
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
        //todo add some link to create new parameter
        $formMapper
            ->add(
                'parentCategory',
                'sonata_type_model_list',
                array(
                    'required'  => false
                )
            )
            ->add('name')
            ->add(
                'parameters',
//                'sonata_type_collection',
//                null,
                'sonata_type_model_autocomplete',
                array(
                    'required'      => false,
                    'property'      => 'name',
                    'placeholder'   => 'Фильтры',
                    'multiple'      => true
                ),
                array(
//                    'edit'      => 'inline',
//                    'inline'    => 'table'
                )
            )
            ;

//        var_dump($formMapper->get('parameters')); die();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('parentCategory')
            ->add('subCategories')
        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
//        parent::configure();
//        //todo think of a way to make this route POST only
//        $collection->add(
//            'api_parameters_for_category',
//            'getParametersForCategory'
//        );
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }
}
