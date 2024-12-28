<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - AMMU-NATION</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Commande Validée</h1>
    <p>Merci pour votre commande, <?= htmlspecialchars($_SESSION['username']); ?>.</p>
    <a href="index.php">Retour à la boutique</a>
</body>
</html>
