<?php

namespace BurgerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
class DefaultController extends Controller
{
    /**
     * Matches 
     *
     * @Route("/burger", name="burger_homepage")
     */
    public function indexAction()
    {
        return $this->render('BurgerBundle:Default:main.html.twig');
    }
    
    /**
     * Matches 
     *
     * @Route("/carte/{type}", name="burger_carte")
     */
    public function carteAction(Request $request, $type = "burger")
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $produits = $repositoryProduit->findByType($type);
        return $this->render('BurgerBundle:Default:carte.html.twig' , array("produits" => $produits));
    }
}
