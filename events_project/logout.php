<?php
session_start();

// On supprime toutes les variables de session
session_unset();

// On détruit la session
session_destroy();

// On renvoie vers l'accueil
header('Location: index.php');
exit;
