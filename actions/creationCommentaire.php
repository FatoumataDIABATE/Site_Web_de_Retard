<?php

    session_start();

    //récupérer les données du formulaire
    $description=filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $idUtilisateur=filter_input(INPUT_POST, "idUtilisateur", FILTER_SANITIZE_STRING);
    $idPoste=filter_input(INPUT_POST, "idPoste", FILTER_SANITIZE_STRING);
    echo("La description du commentaire est " . $description. "<br/>");
    echo("L'id de l'utilisateur qui fait le commentaire est " . $idUtilisateur. "<br/>");
    echo("L'id du post sur lequel le commentaire est fait est " . $idPoste. "<br/>");


    include "../config.php";
    $pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
        , config::UTILISATEUR, config::MOTDEPASSE);

    // insertion du commentaire dans la base de données
    $requete= $pdo->prepare("INSERT INTO commentaire (description, id_utilisateur, id_post, date) values (:description, :id_utilisateur, :id_post, :date)");
    $requete->bindParam(":description", $description);
    $requete->bindParam(":id_utilisateur", $idUtilisateur);
    $requete->bindParam(":id_post", $idPoste);
    $date = new DateTe('now');
    $dateEnChaineDeCaracteres = $date->format('Y-m-d H:i:s');
    $requete->bindParam(":date", $dateEnChaineDeCaracteres);
    $requete->execute();


    header("Location:../accueil.php");

