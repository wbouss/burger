<?php

namespace BurgerBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use BurgerBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use BurgerBundle\Entity\Produit;

class ProduitAdminController extends CRUDController {

    public function createAction() {
        $templateKey = 'edit';
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();
        $form = $this->admin->getForm();
        $form->setData($object);

        if ($form->handleRequest($this->getRequest())->isValid()) {
            // persist if the form was valid and if in preview mode the preview was approved
            if (!$this->isInPreviewMode() || $this->isPreviewApproved()) {
                $image = $object->getImage();
                $image->upload();
                $this->admin->create($object);
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                                'result' => 'ok',
                                'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }
                return new RedirectResponse($this->admin->generateUrl('list'));
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
                    'action' => 'create',
                    'form' => $view,
                    'object' => $object,
        ));
    }

    public function deleteAction($id) {
        $repositoryImage = $this->getDoctrine()->getManager()->getRepository("BurgerBundle:Image");
        $repositoryProduit = $this->getDoctrine()->getManager()->getRepository("BurgerBundle:Produit");
        $produit = $repositoryProduit->find($id);
        $image = $produit->getImage();
        if (file_exists($image->getPath() . $image->getName() . "." . $image->getExtension())) {
            unlink($image->getPath() . $image->getName() . "." . $image->getExtension());
        }
        return parent::deleteAction($id);
    }

    public function editAction($id = null) {

        $form = $this->admin->getForm();

        if ($form->handleRequest($this->getRequest())->isValid()) {
            // persist if the form was valid and if in preview mode the preview was approved
            if (!$this->isInPreviewMode() || $this->isPreviewApproved()) {
                $repositoryImage = $this->getDoctrine()->getManager()->getRepository("BurgerBundle:Image");
                $repositoryProduit = $this->getDoctrine()->getManager()->getRepository("BurgerBundle:Produit");
                $object = $this->admin->getNewInstance();
                $form = $this->admin->getForm();
                $produitFromForm = $form->getData();
                $produitToEdit = $repositoryProduit->find($id);
                $ImageFromForm = $produitFromForm->getImage();
                $newImage = new Image();
                $newImage->setFile($ImageFromForm->getFile());
                $produitToEdit->setIntitule($produitFromForm->getIntitule());
                $produitToEdit->setDescription($produitFromForm->getDescription());
                $produitToEdit->setPrix($produitFromForm->getPrix());
                $em = $this->getDoctrine()->getManager();
                $em->persist($produitToEdit);
                if (!empty($ImageFromForm->getFile())) {
                    $ImageToChange = $repositoryImage->find($ImageFromForm->getId());
                    if (file_exists($ImageToChange->getPath() . $ImageToChange->getName() . "." . $ImageToChange->getExtension())) {
                        unlink($ImageToChange->getPath() . $ImageToChange->getName() . "." . $ImageToChange->getExtension());
                    }
                    $em->remove($ImageToChange);
                    $newImage->upload();
                    $produitToEdit->setImage($newImage);
                    $em->persist($newImage);
                }
                $em->flush();
                return new RedirectResponse($this->admin->generateUrl('list'));
            }
        }
        return parent::editAction($id);
    }

}
