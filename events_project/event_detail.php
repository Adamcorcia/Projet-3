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
</head>
<body>
    <h1><?= htmlspecialchars($event['title']) ?></h1>

    <p><a href="events_list.php">← Retour à la liste des événements</a></p>

    <p><strong>Organisateur :</strong> <?= htmlspecialchars($event['organizer_name']) ?></p>
    <p><strong>Ville :</strong> <?= htmlspecialchars($event['city']) ?></p>

    <?php if (!empty($event['address'])): ?>
        <p><strong>Adresse :</strong> <?= htmlspecialchars($event['address']) ?></p>
    <?php endif; ?>

    <p><strong>Date :</strong> <?= htmlspecialchars($event['date']) ?></p>
    <p><strong>Heure :</strong> <?= htmlspecialchars(substr($event['time'], 0, 5)) ?></p>

    <p><strong>Capacité :</strong> <?= (int)$event['capacity'] ?> places</p>
    <p><strong>Inscrits :</strong> <?= $registeredCount ?> / <?= (int)$event['capacity'] ?></p>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>

    <hr>

    <!-- Bouton d'inscription -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($registeredCount >= $event['capacity']): ?>
            <p style="color:red;">⚠️ L'événement est complet.</p>
        <?php else: ?>
            <form method="post" action="register_event.php">
                <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
                <button type="submit">S'inscrire à cet événement</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>Tu dois être connecté pour t'inscrire.</p>
        <p><a href="login.php">Se connecter</a></p>
    <?php endif; ?>
</body>
</html>
