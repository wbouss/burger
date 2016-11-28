<?php

namespace BurgerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

class DefaultController extends Controller {


    /**
     * Matches 
     *
     * @Route("/", name="burger_homepage")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $produits = $repositoryProduit->findAll();
        return $this->render('BurgerBundle:Default:main.html.twig', array("produits" => $produits));
    }

    /**
     * Matches 
     *
     * @Route("/carte/{type}", name="burger_carte")
     */
    public function carteAction(Request $request, $type = "burger") {
        $em = $this->getDoctrine()->getManager();
        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $produits = $repositoryProduit->findByType($type);
        return $this->render('BurgerBundle:Default:carte.html.twig', array("produits" => $produits));
    }

    /**
     * Matches 
     *
     * @Route("/panier", name="burger_panier")
     */
    public function panierAction(Request $request) {
        return $this->render('BurgerBundle:Default:panier.html.twig');
    }

    /**
     * Matches 
     *
     * @Route("/livraison", name="burger_livraison")
     */
    public function livraisonAction(Request $request) {
        $nb = count($request->getSession()->get("panier")["idProduit"]);
        return $this->render('BurgerBundle:Default:livraison.html.twig', array("nbArticlePanier" => $nb));
    }

    /**
     * Matches 
     *
     * @Route("/moncompte", name="burger_moncompte")
     */
    public function monCompteAction(Request $request) {
        return $this->render('BurgerBundle:Default:moncompte.html.twig');
    }

    /**
     * Matches 
     *
     * @Route("/commander", name="burger_commander")
     */
    public function commanderAction(Request $request) {
        return $this->render('BurgerBundle:Default:panier.html.twig');
    }

    /**
     * Matches 
     *
     * @Route("/panierData", 
     * options = { "expose" = true },
     * name="panier_data")
     */
    public function panierDataAction(Request $request) {
        // on retourne vide si le panier n'existe pas
        if (empty($request->getSession()->get("panier"))) {
            return new response('{"aaData":{}}');
        }
        $em = $this->getDoctrine()->getManager();
        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $session = $request->getSession()->get("panier");
        $retour = array();
        $i = 0;
        foreach ($session["idProduit"] as $s) {
            $format = array();
            $format["IdProduit"] = $session["idProduit"][$i];
            $produit = $repositoryProduit->find($session["idProduit"][$i]);
            $format["libelleProduit"] = $produit->getIntitule();
            $format["prixProduit"] = $produit->getPrix();
            $format["imageProduit"] = $produit->getImage()->getPath() . $produit->getImage()->getName() . "." . $produit->getImage()->getExtension();
            $format["qteProduit"] = $session["qteProduit"][$i];
            $format["prixProduit"] = $session["prixProduit"][$i];
            $format["descriptionProduit"] = $produit->getDescription();
            $retour[] = $format;
            $i++;
        }
        return new response('{"aaData":' . json_encode($retour) . '}');
    }

    /**
     * Matches 
     *
     * @Route("/Ajoutpanier/{produitId}",
     * options = { "expose" = true },
     *  name="burger_ajoutpanier")
     */
    public function AjouterProduitPanierAction(Request $request) {
        $produitId = $request->get("produitId");
        if (!empty($produitId)) {
            $em = $this->getDoctrine()->getManager();
            $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
            $produit = $repositoryProduit->find($produitId);
            $this->ajouterArticle($produit->getId(), 1, $produit->getPrix(), $request);
        }
        return $this->render('BurgerBundle:Default:panier.html.twig');
    }

    function creationPanier($request) {
        $session = $request->getSession();
        if ($session->get("panier") == null) {
            $panier = array();
            $panier["idProduit"] = array();
            $panier["qteProduit"] = array();
            $panier["prixProduit"] = array();
            $panier["verrou"] = false;
            $session->set('panier', $panier);
        }
        return true;
    }

    /**
     * Matches 
     *
     * @Route("/getSession", name="burger_getsession")
     */
    function getSessionAction(Request $request) {

        $session = $request->getSession();
        return new Response(json_encode($session->get("panier")));
    }

    /**
     * Matches 
     *
     * @Route("/supprimerArticlePanier/{produitId}",
     * options = { "expose" = true },
     *  name="burger_supprimerpanier")
     */
    function supprimerArticlePanierAction(Request $request) {
        $produitId = $request->get("produitId");
        if (!empty($produitId)) {
            $this->supprimerArticle($produitId, $request);
        }
        return $this->render('BurgerBundle:Default:panier.html.twig');
    }

    /**
     * Matches 
     *
     * @Route("/reduireArticlePanier/{produitId}",
     * options = { "expose" = true },
     *  name="burger_reduirepanier")
     */
    function reduireArticlePanierAction(Request $request) {
        $produitId = $request->get("produitId");
        if (!empty($produitId)) {
            $this->reduireArticle($produitId, $request);
        }
        return $this->render('BurgerBundle:Default:panier.html.twig');
    }

    function ajouterArticle($idProduit, $qteProduit, $prixProduit, $request) {
        $session = $request->getSession();

        //Si le panier existe
        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
            $p = $session->get("panier");
            //Si le produit existe déjà on ajoute seulement la quantité
            $positionProduit = array_search($idProduit, $p["idProduit"]);

            if ($positionProduit !== false) {
                $p['qteProduit'][$positionProduit] += $qteProduit;
            } else {

                //Sinon on ajoute le produit
                $p["idProduit"][] = $idProduit;
                $p["qteProduit"][] = $qteProduit;
                $p["prixProduit"][] = $prixProduit;
            }

            $session->set("panier", $p);
            return true;
        } else
            return false;
    }

    function reduireArticle($idProduit, $request) {
        $session = $request->getSession();

        //Si le panier existe
        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
            $p = $session->get("panier");
            //Si le produit existe déjà on ajoute seulement la quantité
            $positionProduit = array_search($idProduit, $p["idProduit"]);

            if ($positionProduit !== false) {
                if ($p['qteProduit'][$positionProduit] == 1)
                    $this->supprimerArticle($idProduit, $request);
                else if ($p['qteProduit'][$positionProduit] > 1) {
                    $p['qteProduit'][$positionProduit] -= 1;
                    $session->set("panier", $p);
                } else
                    return false;
            } else {
                //Sinon le produit n'existe pas, on ne fait rien
                return false;
            }
            return true;
        } else
            return false;
    }

    function supprimerArticle($idProduit, $request) {

        //Si le panier existe
        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
            //Nous allons passer par un panier temporaire

            $tmp = array();
            $tmp['idProduit'] = array();
            $tmp['qteProduit'] = array();
            $tmp['prixProduit'] = array();
            $panier = $request->getSession()->get("panier");
            $tmp['verrou'] = $panier["verrou"];
            for ($i = 0; $i < count($panier['idProduit']); $i++) {
                if ($panier['idProduit'][$i] != $idProduit) {
                    $tmp['idProduit'][] = $panier['idProduit'][$i];
                    $tmp['qteProduit'][] = $panier['qteProduit'][$i];
                    $tmp['prixProduit'][] = $panier['prixProduit'][$i];
                }
            }
            //On remplace le panier en session par notre panier temporaire à jour
            $request->getSession()->set("panier", $tmp);
            return true;
        } else
            return false;
    }

    function modifierQTeArticle($libelleProduit, $qteProduit) {
        //Si le panier éxiste
        if (creationPanier() && !isVerrouille()) {
            //Si la quantité est positive on modifie sinon on supprime l'article
            if ($qteProduit > 0) {
                //Recharche du produit dans le panier
                $positionProduit = array_search($libelleProduit, $_SESSION['panier']['libelleProduit']);

                if ($positionProduit !== false) {
                    $_SESSION['panier']['qteProduit'][$positionProduit] = $qteProduit;
                }
            } else
                supprimerArticle($libelleProduit);
        } else
            echo "Un problème est survenu veuillez contacter l'administrateur du site.";
    }

    function MontantGlobal() {
        $total = 0;
        for ($i = 0; $i < count($_SESSION['panier']['libelleProduit']); $i++) {
            $total += $_SESSION['panier']['qteProduit'][$i] * $_SESSION['panier']['prixProduit'][$i];
        }
        return $total;
    }

    function isVerrouille($request) {
        $session = $request->getSession();
        if ($session->get('panier')["verrou"])
            return true;
        else
            return false;
    }

    function compterArticles() {
        if (isset($_SESSION['panier']))
            return count($_SESSION['panier']['libelleProduit']);
        else
            return 0;
    }

    function supprimePanier() {
        unset($_SESSION['panier']);
    }

}
