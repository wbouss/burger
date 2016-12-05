<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UserCustomBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Annotations\Annotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Description of RegistrationController
 *
 * @author wbouss
 */
class SecurityController extends BaseController {
//    
//    public function LoginBisAction() {
//        $csrfToken = $this->has('security.csrf.token_manager')
//            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
//            : null;
//        return $this->container->get('templating')->renderResponse('FOSUserBundle:Security:login_content.html.twig', array(
//                    'last_username' => null,
//                    'error' => null,
//                    'csrf_token' => $csrfToken
//        ));
//    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request) {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        $csrfToken = $this->has('security.csrf.token_manager') ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;

        if ($error != null)
            return $this->container->get('templating')->renderResponse('BurgerBundle:Default:moncompte.html.twig', array(
                        'last_username' => null,
                        'errorL' => "Nom d'utilisateur ou mot de passe incorrect",
                        'csrf_token' => $csrfToken));

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);


        return $this->container->get('templating')->renderResponse('UserCustomBundle:Default:login_content.html.twig', array(
                    'last_username' => null,
                    'error' => $error,
                    'csrf_token' => $csrfToken));

//        return $this->renderLogin(array(
//                    'last_username' => $lastUsername,
//                    'error' => $error,
//                    'csrf_token' => $csrfToken,
//        ));
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data) {
        return $this->render('FOSUserBundle:Security:moncompte.html.twig', $data);
    }

    public function checkAction() {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction() {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

}
