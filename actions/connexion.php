<?php

    session_start();

    //récupérer les données du formulaire
    $pseudo=filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_STRING);
    $password=filter_input(INPUT_POST,"password",FILTER_SANITIZE_STRING);

    /*echo("Mon pseudo est " . $pseudo . " et mon mot de passe est : " . $password . "<br>");*/

    include "../config.php";
    //on va faire l'insert dans la base de données
    //on se connecte au serveur de base de données avec du PDO objet
    $pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
        , config::UTILISATEUR, config::MOTDEPASSE);
    //on crée une requete
    $requete=$pdo->prepare("SELECT pseudo FROM utilisateur WHERE password = (:password) AND pseudo = (:pseudo)");
    $requete->bindParam(":password", $password);
    $requete->bindParam(":pseudo", $pseudo);

    $requete->execute();

    $utilisateur = $requete->fetch();

    if (empty($utilisateur)) {
        /*echo ("Le compte ayant pour pseudo " . $pseudo . " et mot de passe " . $password . " n'existe pas !");*/
        $_SESSION["identifiantsIncorrects"]=true;
        header("Location:../connect.php");
    } else {
        /*echo ("Le compte ayant pour pseudo " . $pseudo . " et mot de passe " . $password . " existe !");*/
        //echo ("Bienvenue " . $pseudo);
        print_r($utilisateur);

        $_SESSION["pseudo"] = $pseudo;
        header("Location:../profil.php");
    }


