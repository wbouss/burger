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
    public static $tarifSup = 0.5;

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
    public function carteAction(Request $request, $type = "Burger") {
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

        return $this->render('BurgerBundle:Default:carte.html.twig', array("typeProduit" => $type, "produits" => $produits, "sauces" => $sauces, "crudites" => $crudites, "boissons" => $boissons, "frites" => $frites, "supplements" => $supplements));
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
     * @Route("/panier", name="burger_panier",
     * options = { "expose" = true })
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
        $montantTotal = $this->MontantGlobal($request);
        if ($type != "" && $type == "magasin")
            $livraison = "magasin";
        if ($type != "" && $type == "domicile")
            $livraison = "domicile";
        else
            $livraison = "magasin";
        return $this->render('BurgerBundle:Default:livraison.html.twig', array("montantTotal" => $montantTotal, "nbArticlePanier" => $nb, "type" => $livraison));
    }

    /**
     * Matches 
     *
     * @Route("/paiement/{livraison}", name="burger_paiement")
     */
    public function paiementAction(Request $request, $livraison = "magasin") {
        $total = $this->MontantGlobal($request);
        $em = $this->getDoctrine()->getManager();

        // on vérifie s'il n'y a pas d'erreur
        // On test si on peut passer des commandes
        $repositoryParametre = $em->getRepository("BurgerBundle:Parametre");
        $autorisation = $repositoryParametre->findOneByNom("Activation des commandes");
        if (empty($autorisation) || $autorisation->getValeur() != "En marche") {
            $message = "Les commandes sont suspendu, veuillez réessayer plus tard";
            return $this->render('BurgerBundle:Default:commander.html.twig', (array("message" => $message, "typeLivraison" => $livraison, "total" => $total)));
        }

        // On test si on peut passer des commandes
        if ($livraison == "domicile" && $total < 15) {
            $message = "Les commandes en dessous de 15 euros ne peuvent être à domicile";
            return $this->render('BurgerBundle:Default:commander.html.twig', (array("message" => $message, "typeLivraison" => $livraison, "total" => $total)));
        }



        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $panier = $request->getSession()->get("panier");
        $translationP = $this->panierD($request);

        // creation de la commande
        $commande = new \BurgerBundle\Entity\Commande();
        $commande->setAdresse($this->getUser()->getAdresse());
        $commande->setCodepostale($this->getUser()->getCodePostale());
        $commande->setVille($this->getUser()->getVille());
        $commande->setInformationComplementairesAdresse($this->getUser()->getInformationComplementairesAdresse());
        $commande->setCodeImmeuble($this->getUser()->getCodeImmeuble());
        $commande->setInterphone($this->getUser()->getInterphone());
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
            $p = $repositoryProduit->find($panier["idProduit"][$i][0]);
            $ligne->setProduit($p->getIntitule());
            $ligne->setQuantite($panier["qteProduit"][$i]);
            $ligne->setPrix($panier["prixProduit"][$i]);
            $ligne->setCommande($commande);
            $ligne->setOptions(json_encode($translationP[$i]["optionsProduit"]));
            $em->persist($ligne);
            $lignes[] = $ligne;
        }

        $em->flush();
        $this->viderPanier($request);

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
                ->setTo(DefaultController::$MailResp)
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

        $repositoryFrite = $em->getRepository("BurgerBundle:TypeFrite");
        $repositorySauce = $em->getRepository("BurgerBundle:Sauce");
        $repositorySupplement = $em->getRepository("BurgerBundle:Supplement");
        $repositoryCrudite = $em->getRepository("BurgerBundle:Crudite");

        $retour = array();
        $i = 0;
        foreach ($session["idProduit"] as $s) {
            $format = array();
            $format["IdProduit"] = $session["idProduit"][$i][0];
            $produit = $repositoryProduit->find($session["idProduit"][$i][0]);
            $format["libelleProduit"] = $produit->getIntitule();
            $format["prixProduit"] = $produit->getPrix();
            $format["imageProduit"] = $produit->getImage()->getPath() . $produit->getImage()->getName() . "." . $produit->getImage()->getExtension();
            $format["qteProduit"] = $session["qteProduit"][$i];
            $format["prixProduit"] = $session["prixProduit"][$i];
            $format["descriptionProduit"] = $produit->getDescription();

            $optionsTranslation = Array();
            if ($produit->getType() == "Burger" || $produit->getType() == "Woop") {

                $friteTranslation = $repositoryFrite->find(intval($session["idProduit"][$i][1][0]))->getNom();
                $sauce1Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][1]))->getNom();
                $sauce2Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][2]))->getNom();
                $boissonTranslation = $repositoryProduit->find(intval($session["idProduit"][$i][1][3]))->getIntitule();
                $supplementTranslation = Array();
                if ($session["idProduit"][$i][1][4] != -1) {
                    foreach ($session["idProduit"][$i][1][4] as $s) {
                        $supplementTranslation[] = $repositorySupplement->find(intval($s))->getNom();
                    }
                } else
                    $supplementTranslation = -1;
                $optionsTranslation[] = $friteTranslation;
                $optionsTranslation [] = $sauce1Translation;
                $optionsTranslation[] = $sauce2Translation;
                $optionsTranslation[] = $boissonTranslation;
                $optionsTranslation[] = $supplementTranslation;
                $optionsTranslation[] = $session["idProduit"][$i][1][5];
                
            } else if ($produit->getType() == "Sandwich") {

                $friteTranslation = $repositoryFrite->find(intval($session["idProduit"][$i][1][0]))->getNom();
                $sauce1Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][1]))->getNom();
                $sauce2Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][2]))->getNom();
                $boissonTranslation = $repositoryProduit->find(intval($session["idProduit"][$i][1][3]))->getIntitule();
                $supplementTranslation = Array();
                $cruditeTranslation = Array();
                if ($session["idProduit"][$i][1][4] != -1) {
                    foreach ($session["idProduit"][$i][1][4] as $s) {
                        $supplementTranslation[] = $repositorySupplement->find(intval($s))->getNom();
                    }
                } else
                    $supplementTranslation = -1;
                if ($session["idProduit"][$i][1][5] != -1) {
                    foreach ($session["idProduit"][$i][1][5] as $c) {
                        $cruditeTranslation[] = $repositoryCrudite->find(intval($c))->getNom();
                    }
                } else {
                    $cruditeTranslation = -1;
                }
                $optionsTranslation[] = $friteTranslation;
                $optionsTranslation [] = $sauce1Translation;
                $optionsTranslation[] = $sauce2Translation;
                $optionsTranslation[] = $boissonTranslation;
                $optionsTranslation[] = $supplementTranslation;
                $optionsTranslation[] = $cruditeTranslation;
            } else if ($produit->getType() == "Tex mex") {

                $sauce1Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][1]))->getNom();
                $supplementTranslation = Array();
                if ($session["idProduit"][$i][1][4] != -1) {
                    foreach ($session["idProduit"][$i][1][4] as $s) {
                        $supplementTranslation[] = $repositorySupplement->find(intval($s))->getNom();
                    }
                } else
                    $supplementTranslation = -1;
                $optionsTranslation[] = -1;
                $optionsTranslation [] = $sauce1Translation;
                $optionsTranslation[] = -1;
                $optionsTranslation[] = -1;
                $optionsTranslation[] = $supplementTranslation;
                $optionsTranslation[] = -1;
            }
            $format["optionsProduit"] = $optionsTranslation;
            $format["typeProduit"] = $produit->getType();

            $retour[] = $format;
            $i++;
        }
        return new response('{"aaData":' . json_encode($retour) . '}');
    }

    /**
     * Matches 
     *
     * @Route("/Ajoutpanier/{idProduit}/{typeProduit}/{frite}/{sauce1}/{sauce2}/{boisson}/{supplement}/{crudite}",
     * options = {"expose" = true },
     *  name="burger_ajoutpanier")
     */
    public function AjouterProduitPanier(Request $request) {

        $produitId = $request->get("idProduit");
        $produitType = $request->get("typeProduit");

        $options = Array($request->get("frite"), $request->get("sauce1"), $request->get("sauce2"), $request->get("boisson"), $request->get("supplement"), $request->get("crudite"));
        if (!empty($produitId)) {
            $em = $this->getDoctrine()->getManager();
            $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
            $produit = $repositoryProduit->find($produitId);
            $this->ajouterArticle($produit->getId(), 1, $produit->getPrix(), $options, $produitType, $request);
        }
        $total = $this->MontantGlobal($request);
        return new Response("ok");
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
     * @Route("/incrementerProduitExistant/{index}",
     * options = {"expose" = true },
     *  name="burger_incrementerproduitexistant")
     */
    public function incrementerProduitExistantAction(Request $request) {
        $session = $request->getSession();
        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
            $sessionPanier = $session->get("panier");
            $sessionPanier["qteProduit"][$request->get("index")] += 1;
            $session->set("panier", $sessionPanier);
        }
        return new Response($this->MontantGlobal($request));
    }

    /**
     * Matches 
     *
     * @Route("/reduireProduitExistant/{index}",
     * options = {"expose" = true },
     *  name="burger_reduireproduitexistant")
     */
    public function reduireProduitExistantAction(Request $request) {
        $session = $request->getSession();
        $index = $request->get("index");
        //Si le panier existe
        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
            $p = $session->get("panier");
            //Si le produit existe déjà on ajoute seulement la quantité

            if ($p['qteProduit'][$index] == 1) {
                $this->supprimerProduitExistantAction($request, $index);
                return new Response($this->MontantGlobal($request));
            } else if ($p['qteProduit'][$index] > 1) {
                $p['qteProduit'][$index] -= 1;
                $session->set("panier", $p);
                return new Response($this->MontantGlobal($request));
            } else
                return new Response("nok");
        }else {
            //Sinon le produit n'existe pas, on ne fait rien
            return new Response("nok");
        }
    }

    function viderPanier(Request $request) {
        $session = $request->getSession();
        $session->set("panier", null);
        return true;
    }

    /**
     * Matches 
     *
     * @Route("/supprimerProduitExistant/{index}",
     * options = {"expose" = true },
     *  name="burger_supprimerproduitexistant")
     */
    public function supprimerProduitExistantAction(Request $request, $index) {
        $index = $request->get("index");
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
                if ($i != $index) {
                    $tmp['idProduit'][] = $panier['idProduit'][$i];
                    $tmp['qteProduit'][] = $panier['qteProduit'][$i];
                    $tmp['prixProduit'][] = $panier['prixProduit'][$i];
                }
            }
            //On remplace le panier en session par notre panier temporaire à jour
            $request->getSession()->set("panier", $tmp);
            return new Response($this->MontantGlobal($request));
        } else
            return new Response("nok");
    }

//    /**
//     * Matches 
//     *
//     * @Route("/supprimerArticlePanier/{produitId}",
//     * options = { "expose" = true },
//     *  name="burger_supprimerpanier")
//     */
//    function supprimerArticlePanierAction(Request $request) {
//        $produitId = $request->get("produitId");
//        if (!empty($produitId)) {
//            $this->supprimerArticle($produitId, $request);
//        }
//        $total = $this->MontantGlobal($request);
//        return $this->render('BurgerBundle:Default:panier.html.twig', array("total" => $total));
//    }
//
//    /**
//     * Matches 
//     *
//     * @Route("/reduireArticlePanier/{produitId}",
//     * options = { "expose" = true },
//     *  name="burger_reduirepanier")
//     */
//    function reduireArticlePanierAction(Request $request) {
//        $produitId = $request->get("produitId");
//        if (!empty($produitId)) {
//            $this->reduireArticle($produitId, $request);
//        }
//        $total = $this->MontantGlobal($request);
//        return $this->render('BurgerBundle:Default:panier.html.twig', array("total" => $total));
//    }

    function ajouterArticle($idProduit, $qteProduit, $prixProduit, $options, $typeProduit, $request) {
        $session = $request->getSession();
        if ($options[4] != -1 ) {
            $arraySupplement = explode(",", $options[4]);
            $options[4] = $arraySupplement;
            $prix = $prixProduit + count($arraySupplement) * DefaultController::$tarifSup;
        }else
            $prix = $prixProduit;

        if ($options[5] != -1 && $typeProduit == "Sandwich") {
            $arrayCrudite = explode(",", $options[5]);
            $options[5] = $arrayCrudite;
        }

        //Si le panier existe
        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
            $p = $session->get("panier");
            //Si le produit existe déjà on ajoute seulement la quantité
            $positionProduit = $this->recherche_produit($idProduit, $options, $p["idProduit"]);

            if ($positionProduit >= 0) {
                $p['qteProduit'][$positionProduit] += $qteProduit;
            } else {
                //Sinon on ajoute le produit
                $p["idProduit"][] = Array($idProduit, $options);
                $p["qteProduit"][] = $qteProduit;
                $p["prixProduit"][] = $prix;
            }

            $session->set("panier", $p);
            return true;
        } else
            return false;
    }

    function recherche_produit($idProduit, $options, $panierProduits) {
        $i = 0;
        $j = 0;
        $memeProduit = false;
        $memeOptions = true;
        $positionPossibleProduit = -1;

        foreach ($panierProduits as $panierProduit) {
            if ($panierProduit[0] == $idProduit) { // même produit, reste a savoir si il on les mêmes options
                $memeProduit = true;
                $j = 0;
                foreach ($panierProduit[1] as $panierProduitOptions) {
                    if ($panierProduitOptions != $options[$j])
                        $memeOptions = false;
                    $j++;
                }
                if ($memeOptions == true)
                    return $i;
                else
                    $memeOptions = true;
            }
            $i++;
        }
        if ($memeProduit)
            return -1;
        else
            return -2;
    }

//
//    function reduireArticle($idProduit, $request) {
//        $session = $request->getSession();
//
//        //Si le panier existe
//        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
//            $p = $session->get("panier");
//            //Si le produit existe déjà on ajoute seulement la quantité
//            $positionProduit = array_search($idProduit, $p["idProduit"]);
//
//            if ($positionProduit !== false) {
//                if ($p['qteProduit'][$positionProduit] == 1)
//                    $this->supprimerArticle($idProduit, $request);
//                else if ($p['qteProduit'][$positionProduit] > 1) {
//                    $p['qteProduit'][$positionProduit] -= 1;
//                    $session->set("panier", $p);
//                } else
//                    return false;
//            } else {
//                //Sinon le produit n'existe pas, on ne fait rien
//                return false;
//            }
//            return true;
//        } else
//            return false;
//    }
//    function supprimerArticle($idProduit, $request) {
//
//        //Si le panier existe
//        if ($this->creationPanier($request) && !$this->isVerrouille($request)) {
//            //Nous allons passer par un panier temporaire
//
//            $tmp = array();
//            $tmp['idProduit'] = array();
//            $tmp['qteProduit'] = array();
//            $tmp['prixProduit'] = array();
//            $panier = $request->getSession()->get("panier");
//            $tmp['verrou'] = $panier["verrou"];
//            for ($i = 0; $i < count($panier['idProduit']); $i++) {
//                if ($panier['idProduit'][$i] != $idProduit) {
//                    $tmp['idProduit'][] = $panier['idProduit'][$i];
//                    $tmp['qteProduit'][] = $panier['qteProduit'][$i];
//                    $tmp['prixProduit'][] = $panier['prixProduit'][$i];
//                }
//            }
//            //On remplace le panier en session par notre panier temporaire à jour
//            $request->getSession()->set("panier", $tmp);
//            return true;
//        } else
//            return false;
//    }
//    function modifierQTeArticle($libelleProduit, $qteProduit) {
//        //Si le panier éxiste
//        if (creationPanier() && !isVerrouille()) {
//            //Si la quantité est positive on modifie sinon on supprime l'article
//            if ($qteProduit > 0) {
//                //Recharche du produit dans le panier
//                $positionProduit = array_search($libelleProduit, $_SESSION['panier']['libelleProduit']);
//
//                if ($positionProduit !== false) {
//                    $_SESSION['panier']['qteProduit'][$positionProduit] = $qteProduit;
//                }
//            } else
//                supprimerArticle($libelleProduit);
//        } else
//            echo "Un problème est survenu veuillez contacter l'administrateur du site.";
//    }

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

//    function compterArticles() {
//        if (isset($_SESSION['panier']))
//            return count($_SESSION['panier']['libelleProduit']);
//        else
//            return 0;
//    }
//    function supprimePanier() {
//        unset($_SESSION['panier']);
//    }

    public function panierD(Request $request) {
        // on retourne vide si le panier n'existe pas
        if (empty($request->getSession()->get("panier"))) {
            return new response('{"aaData":{}}');
        }
        $em = $this->getDoctrine()->getManager();
        $repositoryProduit = $em->getRepository("BurgerBundle:Produit");
        $session = $request->getSession()->get("panier");

        $repositoryFrite = $em->getRepository("BurgerBundle:TypeFrite");
        $repositorySauce = $em->getRepository("BurgerBundle:Sauce");
        $repositorySupplement = $em->getRepository("BurgerBundle:Supplement");
        $repositoryCrudite = $em->getRepository("BurgerBundle:Crudite");

        $retour = array();
        $i = 0;
        foreach ($session["idProduit"] as $s) {
            $format = array();
            $format["IdProduit"] = $session["idProduit"][$i][0];
            $produit = $repositoryProduit->find($session["idProduit"][$i][0]);
            $format["libelleProduit"] = $produit->getIntitule();
            $format["prixProduit"] = $produit->getPrix();
            $format["imageProduit"] = $produit->getImage()->getPath() . $produit->getImage()->getName() . "." . $produit->getImage()->getExtension();
            $format["qteProduit"] = $session["qteProduit"][$i];
            $format["prixProduit"] = $session["prixProduit"][$i];
            $format["descriptionProduit"] = $produit->getDescription();

            $optionsTranslation = Array();
            if ($produit->getType() == "Burger" || $produit->getType() == "Woop") {

                $friteTranslation = $repositoryFrite->find(intval($session["idProduit"][$i][1][0]))->getNom();
                $sauce1Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][1]))->getNom();
                $sauce2Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][2]))->getNom();
                $boissonTranslation = $repositoryProduit->find(intval($session["idProduit"][$i][1][3]))->getIntitule();
                $supplementTranslation = Array();
                if ($session["idProduit"][$i][1][4] != -1) {
                    foreach ($session["idProduit"][$i][1][4] as $s) {
                        $supplementTranslation[] = $repositorySupplement->find(intval($s))->getNom();
                    }
                } else
                    $supplementTranslation = -1;
                $optionsTranslation[] = $friteTranslation;
                $optionsTranslation [] = $sauce1Translation;
                $optionsTranslation[] = $sauce2Translation;
                $optionsTranslation[] = $boissonTranslation;
                $optionsTranslation[] = $supplementTranslation;
                $optionsTranslation[] = $session["idProduit"][$i][1][5];
            } else if ($produit->getType() == "Sandwich") {

                $friteTranslation = $repositoryFrite->find(intval($session["idProduit"][$i][1][0]))->getNom();
                $sauce1Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][1]))->getNom();
                $sauce2Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][2]))->getNom();
                $boissonTranslation = $repositoryProduit->find(intval($session["idProduit"][$i][1][3]))->getIntitule();
                $supplementTranslation = Array();
                $cruditeTranslation = Array();
                if ($session["idProduit"][$i][1][4] != -1) {
                    foreach ($session["idProduit"][$i][1][4] as $s) {
                        $supplementTranslation[] = $repositorySupplement->find(intval($s))->getNom();
                    }
                } else
                    $supplementTranslation = -1;
                if ($session["idProduit"][$i][1][5] != -1) {
                    foreach ($session["idProduit"][$i][1][5] as $c) {
                        $cruditeTranslation[] = $repositoryCrudite->find(intval($c))->getNom();
                    }
                } else {
                    $cruditeTranslation = -1;
                }
                $optionsTranslation[] = $friteTranslation;
                $optionsTranslation [] = $sauce1Translation;
                $optionsTranslation[] = $sauce2Translation;
                $optionsTranslation[] = $boissonTranslation;
                $optionsTranslation[] = $supplementTranslation;
                $optionsTranslation[] = $cruditeTranslation;
            } else if ($produit->getType() == "Tex mex") {

                $sauce1Translation = $repositorySauce->find(intval($session["idProduit"][$i][1][1]))->getNom();
                $supplementTranslation = Array();
                if ($session["idProduit"][$i][1][4] != -1) {
                    foreach ($session["idProduit"][$i][1][4] as $s) {
                        $supplementTranslation[] = $repositorySupplement->find(intval($s))->getNom();
                    }
                } else
                    $supplementTranslation = -1;
                $optionsTranslation[] = -1;
                $optionsTranslation [] = $sauce1Translation;
                $optionsTranslation[] = -1;
                $optionsTranslation[] = -1;
                $optionsTranslation[] = $supplementTranslation;
                $optionsTranslation[] = -1;
            }
            $format["optionsProduit"] = $optionsTranslation;
            $format["typeProduit"] = $produit->getType();

            $retour[] = $format;
            $i++;
        }
        return $retour;
    }

}
