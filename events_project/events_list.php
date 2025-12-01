<?php
session_start();
require_once 'config.php';

$city      = trim($_GET['city'] ?? '');
$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';

$sql = "SELECT e.*, u.name AS organizer_name
        FROM events e
        JOIN users u ON e.organizer_id = u.id
        WHERE 1=1";
$params = [];

if ($city !== '') {
    $sql .= " AND e.city LIKE :city";
    $params[':city'] = '%' . $city . '%';
}

if ($date_from !== '') {
    $sql .= " AND e.date >= :date_from";
    $params[':date_from'] = $date_from;
}

if ($date_to !== '') {
    $sql .= " AND e.date <= :date_to";
    $params[':date_to'] = $date_to;
}

$sql .= " ORDER BY e.date, e.time";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des événements</title>
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
        <h2>Liste des événements</h2>

        <h3>Filtres</h3>
        <form method="get" action="">
            <div class="form-group">
                <label for="city">Ville :</label>
                <input type="text" name="city" id="city"
                       value="<?= htmlspecialchars($city) ?>">
            </div>

            <div class="form-group">
                <label for="date_from">Date à partir de :</label>
                <input type="date" name="date_from" id="date_from"
                       value="<?= htmlspecialchars($date_from) ?>">
            </div>

            <div class="form-group">
                <label for="date_to">Date jusqu'à :</label>
                <input type="date" name="date_to" id="date_to"
                       value="<?= htmlspecialchars($date_to) ?>">
            </div>

            <button type="submit" class="btn">Filtrer</button>
            <a href="events_list.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </section>

    <section class="card">
        <h3>Résultats</h3>

        <?php if (empty($events)): ?>
            <p>Aucun événement ne correspond à ces critères.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($events as $event): ?>
                    <?php
                    $eventId = (int)$event['id'];
                    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
                    $stmtCount->execute([$eventId]);
                    $inscrits = $stmtCount->fetchColumn();
                    $capacity = (int)$event['capacity'];
                    ?>
                    <li class="list-item">
                        <strong><?= htmlspecialchars($event['title']) ?></strong>

                        <?php if ($inscrits >= $capacity): ?>
                            <span class="badge badge-red">Complet</span>
                        <?php else: ?>
                            <span class="badge badge-green"><?= $inscrits ?> / <?= $capacity ?> places</span>
                        <?php endif; ?>

                        <div class="meta">
                            <?= htmlspecialchars($event['city']) ?> –
                            le <?= htmlspecialchars($event['date']) ?> à <?= htmlspecialchars(substr($event['time'], 0, 5)) ?><br>
                            Organisé par : <?= htmlspecialchars($event['organizer_name']) ?>
                        </div>

                        <div style="margin-top:8px;">
                            <a class="btn" href="event_detail.php?id=<?= $eventId ?>">Voir les détails</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
