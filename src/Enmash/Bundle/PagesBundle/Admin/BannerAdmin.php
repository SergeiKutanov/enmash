<?php

namespace Enmash\Bundle\PagesBundle\Admin;

use Enmash\Bundle\PagesBundle\Entity\Banner;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class BannerAdmin extends Admin
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
            ->add('name')
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
        $this->last_position = $this->positionService->getLastPosition($this->getRoot()->getClass());

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
                    'move' => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
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
                    'context'  => 'baner',
                    'required' => false
                )
            )
            ->add(
                'additionalInfoFiles',
                'sonata_type_model_list',
                array(
                    'required'  => false,
                    'by_reference' => false
                ),
                array(
                    'link_parameters'   => array(
                        'provider'  => 'sonata.media.provider.file',
                        'context'   => 'bannerinfofile'
                    )
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

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }
}
