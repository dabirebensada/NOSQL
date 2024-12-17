<?php
session_start();
require 'db_connect.php'; // Connexion à MongoDB

// Vérification de la session
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$db = getMongoDBConnection(); // Connexion à MongoDB
$productsCollection = $db->products; // Sélection de la collection des produits
$products = $productsCollection->find(); // Récupération des produits

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Armurerie</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Bienvenue dans l'armurerie</h1>
    <p>Connecté en tant que <?php echo htmlspecialchars($_SESSION['username']); ?></p>

    <!-- Liste des produits -->
    <h2>Produits</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['description']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?> €</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="logout.php">Déconnexion</a>
</body>
</html>