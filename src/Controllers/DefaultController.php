<?php

namespace UploadFichier\Controllers;

use UploadFichier\Model\EntityManager;

/**
 * Class DefaultController
 * @package UploadFichier\Controllers
 */
class DefaultController extends Controller
{
    /**
     * Render index
     */
    public function indexAction()
    {
        return $this->twig->render('home.html.twig');
    }

    /**
     * Delete image (delete button)
     */
    public function unlinkAction()
    {
        $f = $_GET['file'];
        if (file_exists($f)) unlink($f);
        return $this->twig->render('home.html.twig');
    }

    /**
     * If empty form, display form again
     */
    public function formAction()
    {
        if (empty($_FILES)) {
            return $this->twig->render('form.html.twig');
        }
    }

    /**
     * Verify form and display success page
     */
    public function successAction()
    {
        // on stocke les fichiers uploadés dans la variable $uploaded
        $uploaded = array();

        // on vérifie que des fichiers ont bien été uploadés
        if (!empty($_FILES['upload']['name'][0])) {
            // on les stocke dans la variable $files
            $files = $_FILES['upload'];
            // on stocke les fichiers non uploadés (cas d'erreur) dans la variable $failed
            $failed = array();

            // on crée un tableau qui stocke les extensions autorisées
            $allowed = array('jpg', 'png', 'gif', 'jpeg');

            // on fait une boucle pour parcourir chaque fichier en fonction de sa position dans le tableau de départ
            foreach ($files['name'] as $position => $file_name) {

                // on stocke les fichiers dans le dossier temporaire de php
                $file_tmp = $files['tmp_name'][$position];

                // on récupère la taille du fichier
                $file_size = $files['size'][$position];

                // on récupère l'erreur le cas échéant
                $file_error = $files['error'][$position];

                // on extrait l'extension du nom du fichier
                $file_ext = explode('.', $file_name);

                // on met l'extension en minuscules
                $file_ext = strtolower(end($file_ext));

                // on vérifie si ce fichier peut être uploadé (si l'extension du fichier correspond bien à celles stockées dans le tableau $allowed)
                if (in_array($file_ext, $allowed)) {
                    if ($file_error === 0) {

                        // on vérifie la taille du fichier, qui doit être inférieure à 1Mo
                        if ($file_size <= 1000000) {

                            // on génère un nouveau nom unique pour le fichier uploadé
                            $file_name_new = 'image' . uniqid('', true) . '.' . $file_ext;

                            // on définit un dossier de destination pour le fichier (pour écrire le chemin, on concatène le nom du dossier et le nouveau nom du fichier
                            $file_destination = '../uploads/' . $file_name_new;

                            // on utilise la fonction move_uploaded_file pour déplacer le fichier dans ce dossier
                            if (move_uploaded_file($file_tmp, $file_destination)) {

                                $uploaded[$position] = $file_destination;

                            } else {

                                // si le fichier n'a pas pu être uploadé, on affiche l'erreur
                                $failed[$position] = "[{$file_name}] failed to upload.";
                            }

                        } else {

                            // si le fichier est trop gros, on affiche une erreur
                            $failed[$position] = "[{$file_name}] is too large.";

                        }

                    } else {

                        // si le fichier n'a pas pu être uploadé, on affiche l'erreur
                        $failed[$position] = "[{$file_name}] errored with code {file_error}.";

                    }

                } else {

                    $failed[$position] = "[{$file_name}] file extension '{$file_ext}' is not allowed.";

                }
            }

            // s'il y a une erreur et donc que le tableau $failed est rempli, on l'affiche
            if (!empty($failed)) {
                print_r($failed);
            }

        } else {

            echo 'yo';
        }

        // si tout est ok, on renvoie vers la page de succès
        return $this->twig->render('success.html.twig', array(
            "files" => $uploaded
        ));
    }

}