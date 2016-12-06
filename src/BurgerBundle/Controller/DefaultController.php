<?php

namespace BurgerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

class DefaultController extends Controller {

    private $livraison = "magasin";
    public static $MailResp = "wbouss@gmail.com";

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
        $repositorySauce = $em->getRepository("BurgerBundle:Sauce");
        $repositoryCrudite = $em->getRepository("BurgerBundle:Crudite");
        $repositoryTypeFrite = $em->getRepository("BurgerBundle:TypeFrite");
        $repositorySupplement = $em->getRepository("BurgerBundle:Supplement");
        $produits = $repositoryProduit->findByType($type);
        $boissons = $repositoryProduit->findByType("Boisson");
        $sauces = $repositorySauce->findAll();
        $crudites = $repositoryCrudite->findAll();
        $frites = $repositoryTypeFrite->findAll();
        $supplements = $repositorySupplement->findAll();
        
        return $this->render('BurgerBundle:Default:carte.html.twig', array("typeProduit"=> $type , "produits" => $produits, "sauces"=> $sauces,"crudites"=>$crudites, "boissons" => $boissons, "frites"=>$frites, "supplements"=>$supplements));
    }

    /**
     * Matches 
     *
     * @Route("/changepasseword/", name="burger_changepassword")
     */
    public function changePassewordAction(Request $request) {
        return $this->render('BurgerBundle:Default:changepassword.html.twig', array("etape" => "initial"));
    }

    /**
     * Matches 
     *
     * @Route("/panier", name="burger_panier")
     */
    public function panierAction(Request $request) {
        $total = $this->MontantGlobal($request);
        return $this->render('BurgerBundle:Default:panier.html.twig', array("total" => $total));
    }

    /**
     * Matches 
     *
     * @Route("/register_confirm", name="burger_register_confirm")
     */
    public function registerConfirmAction(Request $request) {
        return $this->render('BurgerBundle:Default:register_confirm.html.twig');
    }

    /**
     * Matches 
     *
     * @Route("/register_confirm", name="burger_update_confirm")
     */
    public function updateConfirmAction(Request $request) {
        return $this->render('BurgerBundle:Default:update_confirm.html.twig');
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
     * @Route("/commander/{livraison}", name="burger_commander")
     */
    public function commanderAction(Request $request, $livraison = "magasin") {
        $total = $this->MontantGlobal($request);
        return $this->render('BurgerBundle:Default:commander.html.twig', (array("typeLivraison" => $livraison, "total" => $total)));
    }

    /**
     * Matches 
     *
     * @Route("/livraison/{type}", name="burger_livraison")
     */
    public function livraisonAction(Request $request, $type = "") {
        $nb = count($request->getSession()->get("panier")["idProduit"]);
        if ($type != "" && $type == "magasin")
            $livraison = "magasin";
        if ($type != "" && $type == "domicile")
            $livraison = "domicile";
        else
            $livraison = "magasin";
        return $this->render('BurgerBundle:Default:livraison.html.twig', array("nbArticlePanier" => $nb, "type" => $livraison));
    }

    /**
     * Matches 
     *
     * @Route("/paiement/{livraison}", name="burger_paiement")
     */
    public function paiementAction(Request $request, $livraison = "magasin") {
        $em = $this->getDoctrine()->getManager();
        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $panier = $request->getSession()->get("panier");

        // creation de la commande
        $commande = new \BurgerBundle\Entity\Commande();
        $commande->setAdresse($this->getUser()->getAdresse());
        $commande->setNom($this->getUser()->getLastName());
        $commande->setTelephone($this->getUser()->getTelephone());
        $commande->setEtat("Emise");
        $commande->setDate(new \DateTime());
        $commande->setLivraison($livraison);
        $em->persist($commande);

        $lignes = array();
        // creation des ligne de commande
        for ($i = 0; $i < count($panier["idProduit"]); $i++) {
            $ligne = new \BurgerBundle\Entity\LigneCommande();
            $p = $repositoryProduit->find($panier["idProduit"][$i]);
            $ligne->setProduit($p->getIntitule());
            $ligne->setQuantite($panier["qteProduit"][$i]);
            $ligne->setPrix($panier["prixProduit"][$i]);
            $ligne->setCommande($commande);
            $em->persist($ligne);
            $lignes[] = $ligne;
        }

        $em->flush();

        /*
         *  mail au fournisseur
         */
        $titre = "Nouvelle commande";
        $from = "noreply-burger@bibiburger.fr";


        $body = $this->get("templating")->render('BurgerBundle:Mail:nouvelleCommandeResponsable.html.twig', array('commande' => $commande, "lignes" => $lignes));

        $message = \Swift_Message::newInstance()
                ->setSubject($titre)
                ->setFrom($from)
                ->setTo(DefaultController::$MailResp)
                ->setBody($body)
                ->setContentType('text/html');
        $this->get('mailer')->send($message);

        /*
         *  mail au client
         */
        $titre = "Votre commande BIBIBURGER";
        $from = "noreply-burger@bibiburger.fr";


        $body = $this->get("templating")->render('BurgerBundle:Mail:nouvelleCommandeClient.html.twig', array('commande' => $commande, "lignes" => $lignes));

        $message = \Swift_Message::newInstance()
                ->setSubject($titre)
                ->setFrom($from)
                ->setTo($this->getUser()->getTelephone())
                ->setBody($body)
                ->setContentType('text/html');
        $this->get('mailer')->send($message);

        return $this->render('BurgerBundle:Default:paiement.html.twig');
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
        $total = $this->MontantGlobal($request);
        return $this->render('BurgerBundle:Default:panier.html.twig', array("total" => $total));
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
        $total = $this->MontantGlobal($request);
        return $this->render('BurgerBundle:Default:panier.html.twig', array("total" => $total));
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
        $total = $this->MontantGlobal($request);
        return $this->render('BurgerBundle:Default:panier.html.twig', array("total" => $total));
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

    function MontantGlobal($request) {
        $panier = $request->getSession()->get("panier");
        $total = 0;
        for ($i = 0; $i < count($panier['idProduit']); $i++) {
            $total += $panier['qteProduit'][$i] * $panier['prixProduit'][$i];
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
