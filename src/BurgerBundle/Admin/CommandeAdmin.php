<?php

namespace BurgerBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CommandeAdmin extends AbstractAdmin {

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id')
                ->add('date')
                ->add('etat')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->add('id')
                ->add('date')
                ->add('etat')
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
            // EDIT
            $formMapper
                    ->add('etat')
            ;
        } else {
            // CREATE
            $formMapper
                    ->add('id')
                    ->add('date')
                    ->add('etat')
            ;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $repositorylignes = $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository("BurgerBundle:LigneCommande");
        $repositorycommande = $this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository("BurgerBundle:Commande");
        $commande = $repositorycommande->find($this->getRoot()->getSubject()->getId());
        $lignes = $repositorylignes->findByCommande($commande);

        $this->ligne = $lignes;
        foreach ($lignes as $l) {
            $custom[] = json_decode($l->getOptions());
        }
        $this->optionsBurger = $custom;

        $showMapper
                ->add('id')
                ->add('date')
                ->add('etat')
                ->add('livraison', "fsl")
                ->add('nom')
                ->add('adresse')
                ->add('telephone')
                ->add('codeImmeuble')
                ->add('interphone')
                ->add('informationComplementairesAdresse')
                ->add('amount')
                ->add('typepaiement')
            ->add("ligne", "entity", array("label" => "les produits", 'template' => 'BurgerBundle:Admin:Produit/ligneproduits.html.twig' // <-- This is the trick
                ))
        ;
    }

    protected function configureRoutes(RouteCollection $collection) {
        // to remove a single route
        $collection->remove('create');
        $collection->remove('delete');
    }

}
