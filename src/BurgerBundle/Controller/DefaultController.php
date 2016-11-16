<?php

namespace BurgerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * Matches /blog exactly
     *
     * @Route("/burger", name="burger_homepage")
     */
    public function indexAction()
    {
        return $this->render('BurgerBundle:Default:index.html.twig');
    }
}
