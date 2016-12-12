<?php

namespace BurgerBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ParametreAdmin extends AbstractAdmin {

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id')
                ->add('nom')
                ->add('valeur')
                ->add('options')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('nom')
                ->add('valeur')
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
        if ($this->id($this->getSubject())) {
            $repositoryparametre = $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository("BurgerBundle:Parametre");
            $parametre = $repositoryparametre->find($this->getRoot()->getSubject()->getId());
            $op = explode(",",$parametre->getOptions());
            $i = 0;
            foreach( $op as $op_e){
                $option_titre[$op[$i]] = $op[$i];
                $i++;
            }
             
            // EDIT
            $formMapper
                    ->add('valeur',"choice", array("choices"=> $option_titre ))
            ;
        } else {
            // CREATE
            $formMapper
                    ->add('nom')
                    ->add('valeur')
                    ->add('options')
            ;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $showMapper
                ->add('nom')
                ->add('valeur')
                ->add('options')
        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
        // to remove a single route
        $collection->remove('create');
        $collection->remove('delete');
    }

}
