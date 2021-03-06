<?php

namespace Enmash\Bundle\StoreBundle\Admin;

use Enmash\Bundle\StoreBundle\Entity\SpecialOffer;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SpecialOfferAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
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
                'type',
                'choice',
                array(
                    'choices'   => SpecialOffer::getTypeArray()
                )
            )
            ->add('startDate', 'sonata_type_date_picker')
            ->add('endDate', 'sonata_type_date_picker')
            ->add('publish')
            ->add('title')
            ->add('store')
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
                'width',
                'choice',
                array(
                    'required'  => false,
                    'choices'   => array(
                        '10'    => 10,
                        '20'    => 20,
                        '30'    => 30,
                        '40'    => 40,
                        '50'    => 50,
                        '60'    => 60,
                    )
                )
            )
            ->add(
                'image',
                'sonata_media_type',
                array(
                    'required'  => false,
                    'provider'  => 'sonata.media.provider.image',
                    'context'   => 'specialoffer'
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
            ->add('title')
        ;
    }

    public function prePersist($offer) {
        $this->fixFewIssues($offer);
    }

    public function preUpdate($offer) {
        $this->fixFewIssues($offer);
    }

    private function fixFewIssues(SpecialOffer $offer) {
        switch ($offer->getType()) {
            case SpecialOffer::TYPE_DISCOUNT:
                $offer->setBody(strip_tags($offer->getBody()));
                $offer->setStore(null);
                break;
            case SpecialOffer::TYPE_BONUS:
                $offer->setStore(null);
                break;
            case SpecialOffer::TYPE_SPECIAL_OFFER:
                $offer->setBody(strip_tags($offer->getBody()));
                break;
        }
    }
}
