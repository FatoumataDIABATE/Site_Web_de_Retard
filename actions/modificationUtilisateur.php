<?php

    session_start();

    //récupérer les données du formulaire
    $email=filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
    $pseudo=filter_input(INPUT_POST,"pseudo",FILTER_SANITIZE_STRING);
    $bio=filter_input(INPUT_POST,"bio",FILTER_SANITIZE_STRING);
    $id=filter_input(INPUT_POST,"id",FILTER_SANITIZE_STRING);

    include "../config.php";

    // on se connecte a la base de données
    $pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
        , config::UTILISATEUR, config::MOTDEPASSE);

    // VERIFICATION DE L'EXISTENCE D'UN AUTRE COMPTE POSSEDANT LA MEME ADRESSE MAIL
    //on crée une requete pour savoir si l'adresse mail existe déjà
    $requete=$pdo->prepare("SELECT * FROM utilisateur WHERE email = (:email) AND id != (:id)");
    $requete->bindParam(":email", $email);
    $requete->bindParam(":id", $id);
    $requete->execute();
    $utilisateursPossedantsLeMemeMail = $requete->fetchAll();



    // VERIFICATION DE L'EXISTENCE D'UN AUTRE COMPTE POSSEDANT LE MEME PSEUDO
    //on crée une requete pour savoir si le pseudo existe déjà
    $requete=$pdo->prepare("SELECT * FROM utilisateur WHERE pseudo = (:pseudo) AND id != (:id)");
    $requete->bindParam(":pseudo", $pseudo);
    $requete->bindParam(":id", $id);
    $requete->execute();
    $utilisateursPossedantsLeMemePseudo = $requete->fetchAll();

    // si le mail existe déjà pour un autre compte que le notre, on enregistre cette information dans la session
    // afin de s'en servir pour afficher le message d'erreur
    if (!empty($utilisateursPossedantsLeMemeMail)) {
        $_SESSION["adresseMailExistantePourUnAutreCompte"] = true;
    }

    // si le pseudo existe déjà pour un autre compte que le notre, on enregistre cette information dans la session
    // afin de s'en servir pour afficher le message d'erreur
    print_r($utilisateursPossedantsLeMemePseudo);
    if (!empty($utilisateursPossedantsLeMemePseudo)) {
        $_SESSION["pseudoExistantePourUnAutreCompte"] = true;
    }

    // Si le pseudo et l'adresse mail n'existe pas, alors on met à jour les infos en base de données
    if(empty($utilisateursPossedantsLeMemeMail) && empty($utilisateursPossedantsLeMemePseudo)) {
        // Sinon on créer le compte et enregistre les informations dans la base de donnée
        // puis, on sauvegarde le pseudo dans la session et on redirige vers la page d'accueil
        $requete=$pdo->prepare("UPDATE utilisateur set email = :email, pseudo = :pseudo, bio = :bio WHERE id = :id");
        $requete->bindParam(":email", $email);
        $requete->bindParam(":pseudo",$pseudo);
        $requete->bindParam(":bio",$bio);
        $requete->bindParam(":id", $id);
        $requete->execute();

        // sert à afficher le message de  succès de l'update
        $_SESSION["modificationReussie"] = true;

        // Mise à jour du pseudo dans la session
        $_SESSION["pseudo"] = $pseudo;
    }

    header("Location:../profil.php");
