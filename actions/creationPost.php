<?php

    session_start();

    //récupérer les données du formulaire
    $description=filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $idUtilisateur=filter_input(INPUT_POST, "idUtilisateur", FILTER_SANITIZE_STRING);
    echo("La description du post est " . $description. "<br/>");


    include "../config.php";
    $pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
        , config::UTILISATEUR, config::MOTDEPASSE);

    // insertion du poste
    $requete= $pdo->prepare("INSERT INTO post (description, id_utilisateur, date) values (:description, :id_utilisateur, :date)");
    $requete->bindParam(":description", $description);
    $requete->bindParam(":id_utilisateur", $idUtilisateur);
    $date = new DateTime('now');
    $dateEnChaineDeCaracteres = $date->format('Y-m-d H:i:s');
    $requete->bindParam(":date", $dateEnChaineDeCaracteres);
    $requete->execute();

    $idPostInsere = $pdo->lastInsertId();
    echo("L'id du post inséré est " . $idPostInsere. "<br/>");


    //print_r($_FILES["photo"]);

    // on créer le dossier dans lequel on stockera les images
    mkdir("../photos/$idPostInsere");

    // nombre de photos
    $nombrePhotos = sizeof($_FILES["photo"]["name"]);

    for ($i=0; $i<$nombrePhotos; $i++) {

        // chemin de destination de la photo
        $cheminDeDestination = "../photos/$idPostInsere/" . basename($_FILES["photo"]["name"][$i]);

        echo("Le chemin de la photo est " . $cheminDeDestination . "<br/>");

        move_uploaded_file($_FILES["photo"]["tmp_name"][$i], $cheminDeDestination);

    }


    header("Location:../accueil.php");

