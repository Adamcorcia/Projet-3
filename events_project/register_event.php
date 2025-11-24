<?php
session_start();
require_once 'config.php';

// 1. Vérifier si utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. Vérifier si un event_id est envoyé
if (!isset($_POST['event_id']) || !ctype_digit($_POST['event_id'])) {
    die("ID d'événement invalide.");
}

$event_id = (int)$_POST['event_id'];
$user_id  = (int)$_SESSION['user_id'];

// 3. Vérifier que l’événement existe
$stmt = $pdo->prepare("SELECT capacity FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    die("Événement introuvable.");
}

// 4. Vérifier si l’utilisateur est déjà inscrit
$stmt = $pdo->prepare("SELECT id FROM registrations WHERE event_id = ? AND user_id = ?");
$stmt->execute([$event_id, $user_id]);
$existing = $stmt->fetch();

if ($existing) {
    die("Tu es déjà inscrit à cet événement.");
}

// 5. Vérifier s'il reste des places
// Compter combien d'inscrits
$stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
$stmt->execute([$event_id]);
$registeredCount = $stmt->fetchColumn();

if ($registeredCount >= $event['capacity']) {
    die("Plus de places disponibles pour cet événement.");
}

// 6. Inscription en base de données
$stmt = $pdo->prepare(
    "INSERT INTO registrations (event_id, user_id)
     VALUES (?, ?)"
);
$stmt->execute([$event_id, $user_id]);

// 7. Redirection vers la page de détail
header("Location: event_detail.php?id=" . $event_id);
exit;
