<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UserCustomBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseControllerr;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
  /**
 * Description of ChangePasseword
 *
 * @author wbouss
 */
class ChangePasswordController extends BaseControllerr {

    /**
     * Change user password.
     *
     * @Route("/userchangepassword", name="usercustom_change_password")
     * @param Request $request
     *
     * @return Response
     */
    public function changePasswordAction(Request $request) {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return new response("a");
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

                $userManager->updateUser($user);

//            if (null === $response = $event->getResponse()) {
//                $url = $this->generateUrl('fos_user_profile_show');
//                $response = new RedirectResponse($url);
//            }
//
//            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
//
//            return $response;
                return $this->render('BurgerBundle:Default:changepassword.html.twig', array(
                            'form' => $form->createView(), "etape" => "modifié"
                ));
            } else {
                return $this->render('BurgerBundle:Default:changepassword.html.twig', array(
                            'form' => $form->createView(), "etape" => "erreur"
                ));
            }
        }
        return $this->render('UserCustomBundle:Default:change_password.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
