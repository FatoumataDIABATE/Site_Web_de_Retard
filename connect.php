<?php
    session_start();
    if(!isset($_SESSION["identifiantsIncorrects"])) {
        $_SESSION["identifiantsIncorrects"] = false;
    }

?>

<!DOCTYPE html>
<html lang="fr">
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
                <h2>Connectez-vous</h2>
            </div>

            <div class="row align-items-center">
                <form method="post" action="actions/connexion.php">
                    <div class="form-group">
                        <label for="pseudo">Pseudo</label>
                        <input name="pseudo" class="form-control" id="pseudo-connect" aria-describedby="pseudoHelp" placeholder="pseudo">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                    </div>

                    <button type="submit" class="btn btn-primary">Se connecter</button>

                    <hr/>

                    <?php
                        if($_SESSION["identifiantsIncorrects"]){
                            echo("<span style='color : red'>Mot de passe ou pseudo incorrect !</span>");

                            // on remet à faux pour que le message disparaisse en cas de rafraichissement de la page
                            $_SESSION["identifiantsIncorrects"] = false;
                        }
                    ?>
                    <br/>
                    <a href="create.php">Créer un compte maintenant</a>
                </form>
            </div>
        </div>
    </body>
</html>
