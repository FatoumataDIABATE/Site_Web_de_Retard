<?php

    session_start();

    //récupérer les données du formulaire
    $email=filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
    $password=filter_input(INPUT_POST,"password",FILTER_SANITIZE_STRING);
    $pseudo=filter_input(INPUT_POST,"pseudo",FILTER_SANITIZE_STRING);
    $bio=filter_input(INPUT_POST,"bio",FILTER_SANITIZE_STRING);
    //echo("Mon adresse mail est " . $email . " et mon mot de passe est : " . $password . "<br/>");


    include "../config.php";

    // on se connecte a la base de données
    $pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
        , config::UTILISATEUR, config::MOTDEPASSE);

    // VERIFICATION DE L'EXISTENCE D'UN COMPTE POSSEDANT LA MEME ADRESSE MAIL
    //on crée une requete pour savoir si l'adresse mail existe déjà
    $requete=$pdo->prepare("SELECT * FROM utilisateur WHERE email = (:email)");
    $requete->bindParam(":email", $email);
    $requete->execute();
    $utilisateursPossedantsLeMemeMail = $requete->fetchAll();


    // VERIFICATION DE L'EXISTENCE D'UN COMPTE POSSEDANT LE MEME PSEUDO
    //on crée une requete pour savoir si le pseudo existe déjà
    $requete=$pdo->prepare("SELECT * FROM utilisateur WHERE pseudo = (:pseudo)");
    $requete->bindParam(":pseudo", $pseudo);
    $requete->execute();
    $utilisateursPossedantsLeMemePseudo = $requete->fetchAll();



    // si le mail existe déjà, on enregistre cette information dans la session
    // afin de s'en servir pour afficher le message d'erreur
    if (!empty($utilisateursPossedantsLeMemeMail)) {
        $_SESSION["adresseMailExiste"] = true;
    }

    // si le pseudo existe déjà, on enregistre cette information dans la session
    // afin de s'en servir pour afficher le message d'erreur
    if (!empty($utilisateursPossedantsLeMemePseudo)) {
        $_SESSION["pseudoExiste"] = true;

    }

    // Si le pseudo ou l'adresse mail existe, on redirige vers la page de connection pour afficher le message d'erreur
    if(!empty($utilisateursPossedantsLeMemeMail) || !empty($utilisateursPossedantsLeMemePseudo)) {
        header("Location:../create.php");
    } else {
        // Sinon on créer le compte et enregistre les informations dans la base de donnée
        // puis, on sauvegarde le pseudo dans la session et on redirige vers la page d'accueil
        $requete=$pdo->prepare("INSERT INTO utilisateur (email,password,pseudo,bio) values (:email ,:password , :pseudo , :bio)");
        $requete->bindParam(":email", $email);
        $requete->bindParam(":password", $password);
        $requete->bindParam(":pseudo",$pseudo);
        $requete->bindParam(":bio",$bio);
        $requete->execute();

        // Redirection vers la page d'accueil
        $_SESSION["pseudo"] = $pseudo;
        header("Location:../accueil.php");
    }
