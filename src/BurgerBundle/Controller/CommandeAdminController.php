<?php

namespace BurgerBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

class CommandeAdminController extends CRUDController {

//    public function readAction() {
//        $templateKey = 'read';
//        if (false === $this->admin->isGranted('READ')) {
//            throw new AccessDeniedException();
//        }
//
//        $object = $this->admin->getNewInstance();
//        $form = $this->admin->getForm();
//        $form->setData($object);
//        $view = $form->createView();
//
//        // set the theme for the current Admin Form
//        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());
//
//        return $this->render($this->admin->getTemplate($templateKey), array(
//                    'action' => 'create',
//                    'form' => $view,
//                    'object' => $object,
//        ));
//    }

}
