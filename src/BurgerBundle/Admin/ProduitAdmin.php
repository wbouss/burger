<?php

namespace BurgerBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProduitAdmin extends AbstractAdmin {

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id')
                ->add('intitule')
                ->add('description')
                ->add('prix')

        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('clone', $this->getRouterIdParameter() . '/clone');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('intitule')
                ->add('description')
                ->add('prix')
                ->add('type')
                ->add('_action', null, array(
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
    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->add('intitule')
                ->add('description')
                ->add('prix')
                ->add('type', "choice", array("choices" => array("Burger" => "Burger", "Woop's" => "Woop's", "Sandwich" => "sandwich", "Tex mex" => "Tex mex", "Dessert" => "Dessert", "Boisson"=>"Boisson")))
                ->add('image', "sonata_type_admin", array(
                    'label' => false,
                    'required' => false))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('intitule')
                ->add('description')
                ->add('prix')
                ->add('type')
                ->add('Image', null, array('template' => 'BurgerBundle:Admin:Produit/show_image.html.twig'))
        ;
    }

}
