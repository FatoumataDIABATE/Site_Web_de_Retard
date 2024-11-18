<?php

session_start();

// RECUPERATION DU PSEUDO DE L'UTILISATEUR
$pseudo = $_SESSION["pseudo"];

// Si pseudo vidé auparavant via une déconnexion, on redirige vers la page de connexion
if ($pseudo == "") {
    header("Location:./connect.php");
}


// si la variable n'est pas définie dans la session, alors on la définit à faux
if (!isset($_SESSION["adresseMailExistantePourUnAutreCompte"])) {
    $_SESSION["adresseMailExistantePourUnAutreCompte"] = false;
}

// si la variable n'est pas définie dans la session, alors on la définit à faux
if (!isset($_SESSION["pseudoExistantePourUnAutreCompte"])) {
    $_SESSION["pseudoExistantePourUnAutreCompte"] = false;
}

// si la variable n'est pas définie dans la session, alors on la définit à faux
if (!isset($_SESSION["modificationReussie"])) {
    $_SESSION["modificationReussie"] = false;
}

// RECUPERATION DES INFORMATIONS DE L'UTILISATEUR GRACE A SON PSEUDO
include "./config.php";
$pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
    , config::UTILISATEUR, config::MOTDEPASSE);

$requete=$pdo->prepare("SELECT id, bio, email FROM utilisateur WHERE pseudo = (:pseudo)");
$requete->bindParam(":pseudo", $pseudo);
$requete->execute();
$result = $requete->fetch();

$id = $result['id'];
$bio = $result['bio'];
$email = $result['email'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projet web</title>
    <script src="js/modeSelector.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div>
    <div class="container-fluid">

        <!-- HEADER, BIENVENUE + DECONNEXION -->
        <div class="row justify-content-between"">
        <div class="col align-self-start">
            <h1 >
                <small>
                    <?php
                    echo("<a href='accueil.php'> Retour vers l'accueil</a>");
                    echo("<a href='profil.php'> <span id='pseudo-accueil'>"  . "</span></a>");
                    ?>
                </small>
            </h1>
        </div>
        <div class="col align-self-end">
            <form method="post" action="actions/deconnexion.php">
                <button id="deconnexion" type="submit" class="btn btn-danger">Se déconnecter</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="row">

            <!-- COLONNE DE CONSULTATION D'INFORMATIONS -->
            <div class="col">
                <div class="row">
                    <h2>Mes infos</h2>
                </div>

                <div class="row align-items-center">
                    <div class="col offset-3">
                        <i class='bi-person-circle' style="font-size: 150px"></i>
                    </div>
                </div>

                <h6>Mon pseudo</h6>
                <?php echo($pseudo);?>
                <hr/>


                <h6>Ma bio</h6>
                <?php echo($bio) ?>
                <hr/>

                <h6>Mon adresse email</h6>
                <?php echo($email) ?>
                <hr/>

            </div>

            <!-- COLONNE DE MODIFICATION -->
            <div class="col offset-1">
                <div class="row">
                    <h2>Modifier mes infos</h2>
                </div>
                <div class="row align-items-center">
                    <form method="post" action="actions/modificationUtilisateur.php">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input value="<?php echo $email ?>" name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="email" required>
                        </div>
                        <div class="form-group">
                            <label for="pseudo">Pseudo</label>
                            <input value="<?php echo $pseudo ?>" name="pseudo" type="text" class="form-control" id="pseudo-connect" aria-describedby="pseudo" placeholder="Votre pseudo" required>
                        </div>
                        <div class="form-group">
                            <label for="bio">Biographie</label>
                            <input value="<?php echo($bio) ?>" name="bio" type="text" class="form-control" id="bio" aria-describedby="bio" placeholder="Votre biograhie" required>
                        </div>
                        <button id="post" type="submit" class="btn btn-primary">Modifier</button>
                    </form>
                    <?php
                    if ($_SESSION["adresseMailExistantePourUnAutreCompte"]) {
                        echo("<span style='color: red'> L'adresse email que vous avez saisi existe déjà pour un autre compte!  </span> <br/>");
                        $_SESSION["adresseMailExistantePourUnAutreCompte"] = false;
                    }

                    if ($_SESSION["pseudoExistantePourUnAutreCompte"]) {
                        echo("<span style='color: red'> Le pseudo que vous avez saisi existe déjà pour un autre compte !  </span> <br/>");
                        $_SESSION["pseudoExistantePourUnAutreCompte"] = false;
                    }

                    if ($_SESSION["modificationReussie"]) {
                        echo("<span style='color: green'> Vos informations ont étées modifiées avec succès !  </span> <br/>");
                        $_SESSION["modificationReussie"] = false;
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- FORMULAIRE DE PUBLICATION D'UN POST-->
        <div class="container">
            <div class="row">
                <h2>Publier un nouveau retard</h2>
            </div>
            <div class="row" id="post-retard-form">
                <div>
                    <form method="post" action="actions/creationPost.php"  enctype="multipart/form-data">
                        <input type="hidden" name="idUtilisateur" value="<?php echo $id ?>">
                        <div class="form-group">
                            <textarea required name="description" type="text" class="form-control" id="description" placeholder="Votre retard"></textarea>
                        </div>
                        <input type="file" name="photo[]" required multiple>

                        <br/> <br/>
                        <button id="post" type="submit" class="btn btn-primary">Publier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
