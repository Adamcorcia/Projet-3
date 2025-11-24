<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Projet événements</title>
</head>
<body>
    <h1>Accueil - Projet événements</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Connecté en tant que <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
           (rôle : <?= htmlspecialchars($_SESSION['user_role']) ?>)</p>
        <p>
            <a href="logout.php">Se déconnecter</a>
        </p>
        <p>
            <a href="create_event.php">Créer un événement</a>
        </p>
        <p>
            <a href="my_events.php">Mes événements</a>
        </p>

    <?php else: ?>
        <p>Tu n'es pas connecté.</p>
        <p>
            <a href="login.php">Se connecter</a> |
            <a href="register.php">Créer un compte</a>
        </p>
    <?php endif; ?>

    <hr>

    <p><a href="events_list.php">Voir tous les événements</a></p>
</body>
</html>

