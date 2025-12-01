<?php
session_start();
require_once 'config.php';

// Vérification de l'id passé dans l'URL
$id = $_GET['id'] ?? null;

if ($id === null || !ctype_digit($id)) {
    die("ID d'événement invalide.");
}

// Récupération des infos de l'événement + nom de l'organisateur
$stmt = $pdo->prepare("
    SELECT e.*, u.name AS organizer_name
    FROM events e
    JOIN users u ON e.organizer_id = u.id
    WHERE e.id = ?
");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Événement introuvable.");
}

// Compter le nombre d'inscrits
$stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
$stmt->execute([$event['id']]);
$registeredCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['title']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="my_events.php">Mes inscriptions</a>

            <?php if ($_SESSION['user_role'] === 'organizer' || $_SESSION['user_role'] === 'admin'): ?>
                <a href="my_created_events.php">Mes événements créés</a>
                <a href="create_event.php">Créer un événement</a>
            <?php endif; ?>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_dashboard.php">Admin</a>
            <?php endif; ?>

            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<main class="main">
    <section class="card">
        <h2><?= htmlspecialchars($event['title']) ?></h2>

        <p class="meta">
            Organisé par <strong><?= htmlspecialchars($event['organizer_name']) ?></strong><br>
            <?= htmlspecialchars($event['city']) ?>
            <?php if (!empty($event['address'])): ?>
                – <?= htmlspecialchars($event['address']) ?>
            <?php endif; ?><br>
            Le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?>
        </p>

        <p>
            <span class="badge badge-blue">Capacité : <?= (int)$event['capacity'] ?> places</span>
            <span class="badge <?= $registeredCount >= $event['capacity'] ? 'badge-red' : 'badge-green' ?>">
                Inscrits : <?= $registeredCount ?> / <?= (int)$event['capacity'] ?>
            </span>
        </p>

        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </section>

    <section class="card">
        <h3>Inscription</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($registeredCount >= $event['capacity']): ?>
                <p class="alert alert-error">⚠️ L'événement est complet.</p>
            <?php else: ?>
                <form method="post" action="register_event.php">
                    <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
                    <button type="submit" class="btn">S'inscrire à cet événement</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p>Tu dois être connecté pour t'inscrire.</p>
            <a class="btn" href="login.php">Se connecter</a>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
