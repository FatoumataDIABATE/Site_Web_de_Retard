<?php

    session_start();
    // destruction de la session, vidage du pseudo dans la session
    $_SESSION["pseudo"] = "";
    session_destroy();

    // Redirection vers la page connect
    header("Location:../connect.php");