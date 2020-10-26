<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alt;

    private $file;

    // on ajoute cet attribut pour y stocker le nom du fichier temporairement
    private $tempFilename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    // on modifie l'accesseur de File, pour prendre en compte le chargement d'un fichier lorsqu'il en existe déjà un autre
    public function setFile($file)
    {
        $this->file = $file;

        return $this;

        // // on vérifie si on avait déjà un fichier pour cette entité
        // if (null !== $this->url) {
        //     // on sauvegarde l'extension du fichier pour le supprimer plus tard
        //     $this->tempFilename = $this->url;
        // }

        // // on réinitialise les valeurs des attributs url et alt
        // $this->url = null;
        // $this->alt = null;
    }

    // /**
    //  * @ORM\PrePersist()
    //  * @ORM\PreUpdate()
    //  */
    // public function preUpload()
    // {
    //     // si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
    //     if (null === $this->file) {
    //         return;
    //     }

    //     // le nom du fichier est son id; on doit juste stocker également son extension
    //     $this->url = uniqid() . '.' . $this->file->guessExtension();

    //     // on génère l'attribut alt à la valeur du nom du fichier
    //     $this->alt = uniqid();
    // }

    // /**
    //  * @ORM\PostPersist()
    //  * @ORM\PostUpdate()
    //  */
    // public function upload()
    // {
    //     // si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
    //     if (null === $this->file) {
    //         return;
    //     }

    //     // si on avait un ancien ficher, on le supprime
    //     if (null !== $this->tempFilename) {
    //         $oldFile = $this->getUploadRootDir() . '/' . uniqid() . '.' . $this->tempFilename;

    //         if (file_exists($oldFile)) {
    //             unlink($oldFile);
    //         }
    //     }

    //     // on déplace le fichier envoyé vers le répertoire de notre choix
    //     $this->file->move(
    //         $this->getUploadDir(), // le répertoire de destination
    //         uniqid() . '.' . $this->url // le nom du fichier à créer, ici (id.extension)
    //     );
    // }

    // /**
    //  * @ORM\PreRemove()
    //  */
    // public function preRemoveUpload()
    // {
    //     // on sauvegarde temporairement le nom du fichier, car il dépend de l'id
    //     $this->tempFilename = $this->getUploadRootDir() . '/' . uniqid() . '.' . $this->url;
    // }

    // /**
    //  * @ORM\PostRemove()
    //  */
    // public function removeUpload()
    // {
    //     // en postRemove, on n'a pas accès à l'id; on utilise notre nom sauvegardé
    //     if (file_exists($this->tempFilename)) {
    //         // on supprime le fichier
    //         unlink($this->tempFilename);
    //     }
    // }

    // public function getUploadDir()
    // {
    //     // on retourne le chemin relatif vers l'image pour un navigateur
    //     return $this->container->getParameter('upload_dir');
    // }

    // protected function getUploadRootDir()
    // {
    //     // on retourne le chemin relatif vers l'image pour notre code php
    //     return $this->getUploadDir();
    // }
}
