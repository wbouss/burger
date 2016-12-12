<?php

namespace BurgerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LigneCommande
 *
 * @ORM\Table(name="ligne_commande")
 * @ORM\Entity(repositoryClass="BurgerBundle\Repository\LigneCommandeRepository")
 */
class LigneCommande {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="produit", type="string")   
     **/
    private $produit;
    
    /**
     *
     * @ORM\Column(name="quantite", type="integer")   
     **/
    private $quantite;
 
    /**
     *
     * @ORM\Column(name="prix", type="float")   
     **/
    private $prix;
    
  
     /**
     * @var string
     *
     * @ORM\Column(name="options", type="string", length=255)
     */
    private $options;
    
    /**
     * @ORM\ManyToOne(targetEntity="BurgerBundle\Entity\Commande")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;
    
    

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    function getProduit() {
        return $this->produit;
    }

    function getCommande() {
        return $this->commande;
    }

    function setProduit($produit) {
        $this->produit = $produit;
    }

    function setCommande($commande) {
        $this->commande = $commande;
    }
    
    function getQuantite() {
        return $this->quantite;
    }

    function getPrix() {
        return $this->prix;
    }

    function setQuantite($quantite) {
        $this->quantite = $quantite;
    }

    function setPrix($prix) {
        $this->prix = $prix;
    }

    function getOptions() {
        return $this->options;
    }

    function setOptions($options) {
        $this->options = $options;
    }



}
