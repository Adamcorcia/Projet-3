<?php
session_start();
require_once 'config.php';

// 1. Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = "";

// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $date        = $_POST['date'] ?? '';
    $time        = $_POST['time'] ?? '';
    $capacity    = $_POST['capacity'] ?? '';

    // Vérifications simples
    if ($title === '')        $errors[] = "Le titre est obligatoire.";
    if ($description === '')  $errors[] = "La description est obligatoire.";
    if ($city === '')         $errors[] = "La ville est obligatoire.";
    if ($date === '')         $errors[] = "La date est obligatoire.";
    if ($time === '')         $errors[] = "L'heure est obligatoire.";
    if (!ctype_digit((string)$capacity) || (int)$capacity <= 0) {
        $errors[] = "La capacité doit être un nombre positif.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO events (organizer_id, title, description, city, address, date, time, capacity)
             VALUES (:organizer_id, :title, :description, :city, :address, :date, :time, :capacity)"
        );

        $stmt->execute([
            ':organizer_id' => $_SESSION['user_id'],
            ':title'        => $title,
            ':description'  => $description,
            ':city'         => $city,
            ':address'      => $address,
            ':date'         => $date,
            ':time'         => $time,
            ':capacity'     => (int)$capacity,
        ]);

        $success = "Événement créé avec succès ✅";
        // On vide les champs pour le formulaire
        $title = $description = $city = $address = $date = $time = $capacity = "";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un événement</title>
</head>
<body>
    <h1>Créer un événement</h1>

    <p><a href="index.php">← Retour à l'accueil</a></p>

    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color:green;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label for="title">Titre :</label><br>
            <input type="text" name="title" id="title" required
                   value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
        </div>

        <div>
            <label for="description">Description :</label><br>
            <textarea name="description" id="description" rows="4" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
        </div>

        <div>
            <label for="city">Ville :</label><br>
            <input type="text" name="city" id="city" required
                   value="<?= isset($city) ? htmlspecialchars($city) : '' ?>">
        </div>

        <div>
            <label for="address">Adresse (optionnel) :</label><br>
            <input type="text" name="address" id="address"
                   value="<?= isset($address) ? htmlspecialchars($address) : '' ?>">
        </div>

        <div>
            <label for="date">Date :</label><br>
            <input type="date" name="date" id="date" required
                   value="<?= isset($date) ? htmlspecialchars($date) : '' ?>">
        </div>

        <div>
            <label for="time">Heure :</label><br>
            <input type="time" name="time" id="time" required
                   value="<?= isset($time) ? htmlspecialchars($time) : '' ?>">
        </div>

        <div>
            <label for="capacity">Capacité (nombre de places) :</label><br>
            <input type="number" name="capacity" id="capacity" required
                   value="<?= isset($capacity) ? htmlspecialchars($capacity) : '' ?>">
        </div>

        <button type="submit">Créer l'événement</button>
    </form>
</body>
</html>
