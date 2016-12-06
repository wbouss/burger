<?php

namespace BurgerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BurgerBundle\Controller\ImageAdminController;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="BurgerBundle\Repository\ImageRepository")
 */
class Image {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
     /**
     * @Assert\File(
     *     maxSize = "5024k",
     *     mimeTypes = {"image/png", "image/jpeg"},
     *     mimeTypesMessage = "le fichier doit être un format image"
     * )
     */
    private $file;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=1000)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=10)
     */
    private $extension;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Image
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Image
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath() {
        return $this->path;
    }
    
    function getExtension() {
        return $this->extension;
    }

    function setExtension($extension) {
        $this->extension = $extension;
    }

    
    function getFile() {
        return $this->file;
    }

    function setFile($file) {
        $this->file = $file;
    }

    
 function upload() {
        $fullname = $this->file->getClientOriginalName();
        $arrayextension = explode(".", $fullname);
        $nb = count($arrayextension);

        $this->name = $this->random(10);
        $this->name .= $arrayextension[0];

        $this->extension = $arrayextension[$nb - 1];
        $this->path = ImageAdminController::$FileDirectory;
        $this->file->move($this->path, $this->name . "." . $this->extension);
    }

    function random($car) {
        $string = "";
        $chaine = "abcdefghijklmnpqrstuvwxy";
        srand((double) microtime() * 1000000);
        for ($i = 0; $i < $car; $i++) {
            $string .= $chaine[rand() % strlen($chaine)];
        }
        return $string;
    }
    
  


}
