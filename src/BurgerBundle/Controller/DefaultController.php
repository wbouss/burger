<?php

namespace BurgerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * @Route("/carte", name="burger_carte")
     */
    public function carteAction()
    {
        return $this->render('BurgerBundle:Default:carte.html.twig');
    }
}
