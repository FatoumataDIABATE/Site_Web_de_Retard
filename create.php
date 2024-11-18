<?php
    session_start();

    // si la variable n'est pas définie dans la session, alors on la définit à faux
    if (!isset($_SESSION["adresseMailExiste"])) {
        $_SESSION["adresseMailExiste"] = false;
    }

    // si la variable n'est pas définie dans la session, alors on la définit à faux
    if (!isset($_SESSION["pseudoExiste"])) {
        $_SESSION["pseudoExiste"] = false;
    }

?>

<!DOCTYPE html>
<html lang="fr">s
<head>
    <meta charset="UTF-8">
    <title>Projet web</title>
    <script src="js/modeSelector.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
    <body>
        <div class="container">
            <div class="row">
                <h2>Créez votre compte</h2>
            </div>

            <div class="row align-items-center">
                <form method="post" action="actions/creationUtilisateur.php">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="email" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="pseudo">Pseudo</label>
                        <input name="pseudo" type="text" class="form-control" id="pseudo-connect" aria-describedby="pseudo" placeholder="Votre pseudo" required>
                    </div>
                    <div class="form-group">
                        <label for="bio">Biographie</label>
                        <input name="bio" type="text" class="form-control" id="bio" aria-describedby="bio" placeholder="Votre biograhie" required>
                    </div>
                    <?php
                        if ($_SESSION["adresseMailExiste"]) {
                            echo("<span style='color: red'> L'adresse email que vous avez saisi existe déjà !  </span> <br/>");
                            $_SESSION["adresseMailExiste"] = false;
                        }

                        if ($_SESSION["pseudoExiste"]) {
                            echo("<span style='color: red'> Le pseudo que vous avez saisi existe déjà !  </span> <br/>");
                            $_SESSION["pseudoExiste"] = false;
                        }
                    ?>
                    <button type="submit" class="btn btn-primary">Créer compte</button>
                </form>
            </div>
            <div class="row">
                <a href="./connect.php">Retour vers la page de connexion
            </div>
        </div>
    </body>
</html>
