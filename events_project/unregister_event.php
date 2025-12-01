<?php
session_start();
require_once 'config.php';

// Vérifier utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id  = (int)$_SESSION['user_id'];
$event_id = $_POST['event_id'] ?? null;

if (!$event_id || !ctype_digit($event_id)) {
    die("ID d'événement invalide.");
}

$event_id = (int)$event_id;

// Vérifier inscription
$stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$user_id, $event_id]);
$registration = $stmt->fetch();

if (!$registration) {
    die("Tu n'es pas inscrit à cet événement.");
}

// Supprimer l'inscription
$stmt = $pdo->prepare("DELETE FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$user_id, $event_id]);

header("Location: my_events.php");
exit;
