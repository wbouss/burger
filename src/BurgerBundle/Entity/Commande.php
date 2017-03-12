<?php

namespace BurgerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="BurgerBundle\Repository\CommandeRepository")
 */
class Commande {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;



    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codepostale", type="string", length=255)
     */
    private $codepostale;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="informationComplementairesAdresse", type="string", length=255, nullable=true)
     */
    private $informationComplementairesAdresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codeImmeuble", type="string", length=100, nullable=true)
     */
    private $codeImmeuble;

    /**
     * @var string
     *
     * @ORM\Column(name="interphone", type="string", length=100, nullable=true)
     */
    private $interphone;

    /**
     *
     * @ORM\Column(name="telephone", type="integer")
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="livraison", type="string", length=255)
     */
    private $livraison;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commande
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Commande
     */
    public function setEtat($etat) {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat() {
        return $this->etat;
    }

    function getNom() {
        return $this->nom;
    }

    function getAdresse() {
        return $this->adresse;
    }

    function setNom($nom) {
        $this->nom = $nom;
    }

    function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    function getLivraison() {
        return $this->livraison;
    }

    function setLivraison($livraison) {
        $this->livraison = $livraison;
    }

    function getTelephone() {
        return $this->telephone;
    }

    function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    function __construct() {
        $this->etat = "Emise";
    }

    function getCodepostale() {
        return $this->codepostale;
    }

    function getVille() {
        return $this->ville;
    }

    function getInformationComplementairesAdresse() {
        return $this->informationComplementairesAdresse;
    }

    function getOptions() {
        return $this->options;
    }

    function setCodepostale($codepostale) {
        $this->codepostale = $codepostale;
    }

    function setVille($ville) {
        $this->ville = $ville;
    }

    function setInformationComplementairesAdresse($informationComplementairesAdresse) {
        $this->informationComplementairesAdresse = $informationComplementairesAdresse;
    }

    function getCodeImmeuble() {
        return $this->codeImmeuble;
    }

    function getInterphone() {
        return $this->interphone;
    }

    function setCodeImmeuble($codeImmeuble) {
        $this->codeImmeuble = $codeImmeuble;
    }

    function setInterphone($interphone) {
        $this->interphone = $interphone;
    }
    
    function getMagasin() {
        return $this->magasin;
    }

    function setMagasin($magasin) {
        $this->magasin = $magasin;
    }



}
