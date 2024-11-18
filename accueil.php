<?php
    session_start();

    // RECUPERATION DU PSEUDO DE L'UTILISATEUR
    $pseudo = $_SESSION["pseudo"];

    // Si pseudo vidé auparavant via une déconnexion, on redirige vers la page de connexion
    if ($pseudo == "") {
        header("Location:./connect.php");
    }


    // RECUPERATION DE L'ID DE L'UTILISATEUR GRACE A SON PSEUDO
    include "./config.php";
    $pdo=new PDO("mysql:host=".config::SERVEUR.";dbname=".config::BASEDEDONNEES
        , config::UTILISATEUR, config::MOTDEPASSE);
    $requete=$pdo->prepare("SELECT id FROM utilisateur WHERE pseudo = (:pseudo)");
    $requete->bindParam(":pseudo", $pseudo);
    $requete->execute();
    $result = $requete->fetch();

    $idUtilisateur = $result['id'];


    // RECUPERATION DE TOUS LES POSTES EN BASE DE DONNEES
    $requete=$pdo->prepare("SELECT utilisateur.pseudo, post.id, post.description, post.date FROM utilisateur JOIN post on utilisateur.id = post.id_utilisateur
                                                                     order by post.date desc;");
    $requete->execute();
    // On récupère tous les posts
    $posts = $requete->fetchAll();


    // Récupération de tous les commentaires associés à un post, groupés par post
    $requete=$pdo->prepare("SELECT commentaire.id_post, GROUP_CONCAT(commentaire.description, '|' , utilisateur.pseudo, '|', commentaire.date) AS infos_commentaires FROM commentaire JOIN utilisateur on utilisateur.id = commentaire.id_utilisateur group by commentaire.id_post;");
    $requete->execute();
    // On récupère tous les commentaires
    $commentairesRecuperesDeLaBaseParPost = $requete->fetchAll();

    $commentairesParPostsMisEnFormes = array();

    // On parcourt tous les commentaires récupérés de la base
    foreach($commentairesRecuperesDeLaBaseParPost as $commentairesParPost) {

        // on récupère les commentaires sous forme de tableau de string
        $commentaires = explode(",", $commentairesParPost["infos_commentaires"]);

        // Pour chaque commentaire associé au post
        $commentairesMisEnForme = array();
        foreach ($commentaires as $commentaire) {
            // On récupère les éléments du commentaire : sa description, le pseudo de l'auteur et la date de publication
            $elements = explode("|", $commentaire);
            $donnees = [
                "description" => $elements[0],
                "pseudo" => $elements[1],
                "date" => $elements[2],
            ];
            array_push($commentairesMisEnForme, $donnees);
        }

        // On trie le tableau par date decroissantes
        usort($commentairesMisEnForme, function ($a, $b) {return $a['date'] > $b['date'];});

        // On associe l'ensemble des commentaires à l'id
        $commentairesParPostsMisEnFormes[$commentairesParPost['id_post']] = $commentairesMisEnForme;
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container-fluid">

        <!-- HEADER, BIENVENUE + DECONNEXION -->
        <div class="row justify-content-between"">
            <div class="col align-self-start">
                <h1 >
                    <small>
                        <?php
                            echo("Bienvenue ");
                            echo("<a href='profil.php'> <span id='pseudo-accueil'>" . $pseudo . "</span></a>");
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

        <!-- FORMULAIRE DE PUBLICATION D'UN POST-->
        <div class="container">
            <div class="row">
                <h2>Publier un nouveau retard</h2>
            </div>
            <div class="row" id="post-retard-form">
                <div>
                    <form method="post" action="actions/creationPost.php"  enctype="multipart/form-data">
                        <input type="hidden" name="idUtilisateur" value="<?php echo $idUtilisateur ?>">
                        <div class="form-group">
                            <textarea required name="description" type="text" class="form-control" id="description" placeholder="Votre retard"></textarea>
                        </div>
                        <input type="file" name="photo[]" required multiple>

                        <br/> <br/>
                        <button id="post" type="submit" class="btn btn-primary">Publier</button>
                    </form>
                </div>
            </div>

            <!-- div contenant tous les posts -->
            <div class="container">
                <?php
                    // On parcours la liste des postes
                    foreach ($posts as $post) {
                ?>

                <!-- div englobant un seul post -->
                <div id='post-retard'>
                    <!-- affichage du pseudo -->
                    <div id="pseudo">
                        <i class='bi-person-circle'></i>
                        <?php echo $post['pseudo'] ?>
                    </div>

                    <div id='retard-description'>
                        <!-- affichage de la description -->
                        <?php echo $post['description'] ?>
                    </div>
                    <div>
                        <i class='bi-clock'></i>
                        <!-- affichage de la date -->
                        <?php echo $post['date'] ?>
                    </div>


                    <!-- AFFICHAGE DES PHOTOS -->
                    <?php
                        // l'id du post
                        $idPoste = $post['id'];

                        // je vérifie si le dossier existe
                        if (is_dir("photos/$idPoste")) {

                            //je récupère la liste des photos du poste
                            $photos = scandir("photos/$idPoste");
                            //je parcours la liste des photos
                            foreach ($photos as $photo) {
                                //je verifie que c'est bien un fichier
                                if (is_file("photos/$idPoste/$photo")) {
                                    echo(
                                    "<img id='image' width='700' height='500' src = 'photos/$idPoste/$photo' >"
                                    );
                                }
                            }
                        }
                        ?>
                    if

                    <!-- ZONE D'AFFICHAGE DES COMMENTAIRES -->
                    <div>
                        <?php

                            // Si le post possède un ou plusieurs commentaires
                            if (array_key_exists($idPoste, $commentairesParPostsMisEnFormes)) {
                                $commentairesDuPost = $commentairesParPostsMisEnFormes[$idPoste];

                                // On parcours tous les commentaires du poste
                                foreach ($commentairesDuPost as $commentaire) {

                                    // Pour chaque commentaire, on affiche ses informations.
                        ?>
                                <div id="bloc-commentaire" class="container">
                                    <div class="row">
                                        <div class="col">
                                            <i class='bi-person-circle'></i>
                                            <?php echo($commentaire["pseudo"]) ; ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <?php echo($commentaire["description"]) ; ?>
                                        </div>
                                    </div>
                                </div>
                                    <?php
                                    }
                                ?>

                        <?php

                            }
                        ?>
                    </div>
                    <form method="post" action="actions/creationCommentaire.php">
                        <input type="hidden" name="idUtilisateur" value="<?php echo $idUtilisateur ?>">
                        <input type="hidden" name="idPoste" value="<?php echo $idPoste ?>">

                        <div id="conteneur-commentaire" class="row">
                            <div class="form-group col-9">
                                <input required name="description" type="text" class="form-control" id="description" placeholder="Réagissez à ce retard">
                            </div>

                            <div class="col-3">
                                <button id="post" type="submit" class="btn btn-primary">Commenter</button>
                            </div>
                        </div>
                    </form>

                    <hr id='separateur'/>

                </div>
                        <?php
                    }
                    ?>
            </div>
        </div>

    </body>
    </html>


