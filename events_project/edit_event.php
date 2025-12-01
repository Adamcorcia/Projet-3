<?php
session_start();
require_once 'config.php';

// Vérifier connecté + organizer/admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['user_role'] !== 'organizer' && $_SESSION['user_role'] !== 'admin') {
    die("Accès refusé : tu dois être organisateur ou admin.");
}

$user_id = (int)$_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if ($id === null || !ctype_digit($id)) {
    die("ID d'événement invalide.");
}

$id = (int)$id;

// Si admin → peut éditer tout ; si organizer → seulement ses événements
if ($_SESSION['user_role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND organizer_id = ?");
    $stmt->execute([$id, $user_id]);
}
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Événement introuvable ou tu n'es pas autorisé à le modifier.");
}

$title       = $event['title'];
$description = $event['description'];
$city        = $event['city'];
$address     = $event['address'];
$date        = $event['date'];
$time        = $event['time'];
$capacity    = $event['capacity'];

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $date        = $_POST['date'] ?? '';
    $time        = $_POST['time'] ?? '';
    $capacity    = $_POST['capacity'] ?? '';

    if ($title === '')        $errors[] = "Le titre est obligatoire.";
    if ($description === '')  $errors[] = "La description est obligatoire.";
    if ($city === '')         $errors[] = "La ville est obligatoire.";
    if ($date === '')         $errors[] = "La date est obligatoire.";
    if ($time === '')         $errors[] = "L'heure est obligatoire.";
    if (!ctype_digit((string)$capacity) || (int)$capacity <= 0) {
        $errors[] = "La capacité doit être un nombre positif.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE events
            SET title = :title,
                description = :description,
                city = :city,
                address = :address,
                date = :date,
                time = :time,
                capacity = :capacity
            WHERE id = :id
        ");

        $stmt->execute([
            ':title'        => $title,
            ':description'  => $description,
            ':city'         => $city,
            ':address'      => $address,
            ':date'         => $date,
            ':time'         => $time,
            ':capacity'     => (int)$capacity,
            ':id'           => $id
        ]);

        $success = "Événement mis à jour ✅";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'événement - <?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1 class="header-title">Projet Événements</h1>
    <nav class="header-nav">
        <a href="index.php">Accueil</a>
        <a href="events_list.php">Événements</a>
        <a href="my_created_events.php">Mes événements créés</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="main">
    <section class="card">
        <h2>Modifier l'événement</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="title">Titre :</label>
                <input type="text" name="title" id="title" required
                       value="<?= htmlspecialchars($title) ?>">
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div class="form-group">
                <label for="city">Ville :</label>
                <input type="text" name="city" id="city" required
                       value="<?= htmlspecialchars($city) ?>">
            </div>

            <div class="form-group">
                <label for="address">Adresse (optionnel) :</label>
                <input type="text" name="address" id="address"
                       value="<?= htmlspecialchars($address) ?>">
            </div>

            <div class="form-group">
                <label for="date">Date :</label>
                <input type="date" name="date" id="date" required
                       value="<?= htmlspecialchars($date) ?>">
            </div>

            <div class="form-group">
                <label for="time">Heure :</label>
                <input type="time" name="time" id="time" required
                       value="<?= htmlspecialchars(substr($time, 0, 5)) ?>">
            </div>

            <div class="form-group">
                <label for="capacity">Capacité :</label>
                <input type="number" name="capacity" id="capacity" required
                       value="<?= (int)$capacity ?>">
            </div>

            <button type="submit" class="btn">Enregistrer les modifications</button>
        </form>
    </section>
</main>

</body>
</html>

