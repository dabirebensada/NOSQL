<?php
session_start();
require 'db_connect.php'; // Connexion à MongoDB

// Vérification de la session et des droits d'administrateur
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
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
    <title>Admin - Armurerie</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Bienvenue dans l'espace admin</h1>
    <p>Connecté en tant que <?php echo htmlspecialchars($_SESSION['username']); ?></p>

    <!-- Liste des produits -->
    <h2>Produits</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['description']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?> €</td>
            <td>
                <a href="updateproduct.php?id=<?php echo $product['_id']; ?>">Modifier</a>
                <a href="deleteproduct.php?id=<?php echo $product['_id']; ?>">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Formulaire pour ajouter un produit -->
    <h2>Ajouter un produit</h2>
    <form action="addproduct.php" method="POST">
        <div>
            <label for="name">Nom du produit :</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div>
            <label for="price">Prix :</label>
            <input type="number" id="price" name="price" required>
        </div>
        <button type="submit">Ajouter le produit</button>
    </form>

    <a href="logout.php">Déconnexion</a>
</body>
</html>