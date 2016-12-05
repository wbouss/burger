<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BurgerBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=3000)
     */
    private $adresse;
    
     /**
     *
     * @ORM\Column(name="telephone", type="integer", length=3000)
     */
    private $telephone;
    
        /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=3000)
     */
    private $firstName;

    
    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=3000)
     */
    private $lastName;


    /**
     * @var string
     *
     * @ORM\Column(name="codepostale", type="string", length=3000)
     */
    private $codepostale;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=3000)
     */
    private $ville;

    public function __construct() {
        parent::__construct();
        // your own logic
    }
    
    function getAdresse() {
        return $this->adresse;
    }

    function getCodepostale() {
        return $this->codepostale;
    }

    function getVille() {
        return $this->ville;
    }

    function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    function setCodepostale($codepostale) {
        $this->codepostale = $codepostale;
    }

    function setVille($ville) {
        $this->ville = $ville;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function getTelephone() {
        return $this->telephone;
    }

    function setTelephone($telephone) {
        $this->telephone = $telephone;
    }




}
