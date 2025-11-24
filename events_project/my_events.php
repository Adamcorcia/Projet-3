<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Requête : récupérer les événements où l'utilisateur est inscrit
$stmt = $pdo->prepare("
    SELECT e.*, u.name AS organizer_name
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    JOIN users u ON e.organizer_id = u.id
    WHERE r.user_id = ?
    ORDER BY e.date, e.time
");
$stmt->execute([$user_id]);
$myEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes événements</title>
</head>
<body>
    <h1>Mes événements inscrits</h1>

    <p><a href="index.php">← Retour à l'accueil</a></p>

    <?php if (empty($myEvents)): ?>
        <p>Tu n'es inscrit à aucun événement pour le moment.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($myEvents as $event): ?>
                <li>
                    <strong><?= htmlspecialchars($event['title']) ?></strong><br>
                    <?= htmlspecialchars($event['city']) ?> – 
                    le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?><br>
                    Organisé par : <?= htmlspecialchars($event['organizer_name']) ?><br>

                    <a href="event_detail.php?id=<?= $event['id'] ?>">Voir les détails</a>
                </li>
                <hr>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
