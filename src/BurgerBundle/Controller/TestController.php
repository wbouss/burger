<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BurgerBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Description of TestController
 *
 * @author wbouss
 */
class TestController extends Controller {
    
    
   /**
     * Matches 
     *
     * @Route("/testmail", name="test_mail")
     */
    public function indexAction() {
        /*
         *  mail au fournisseur
         */
        $titre = "Nouvelle commande";
        $from = "noreply-burger@bibiburger.fr";


        $body = $this->get("templating")->render('BurgerBundle:Mail:test.html.twig');

        $message = \Swift_Message::newInstance()
                ->setSubject($titre)
                ->setFrom($from)
                ->setTo("wbouss@gmail.com")
                ->setBody($body)
                ->setContentType('text/html');
        $this->get('mailer')->send($message);

        return $this->render('BurgerBundle:Test:main.html.twig');
        
    }}
