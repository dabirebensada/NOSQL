<?php
session_start();
require 'db_connect.php'; 

// Vérification de la session
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$db = getMongoDBConnection();
$cartCollection = $db->cart;

// Récupérer le panier de l'utilisateur
$username = $_SESSION['username'];
$cart = $cartCollection->findOne(['username' => $username]);

$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - AMUNATION</title>
    <link rel="stylesheet" href="stylecart.css">
</head>
<body>
    <h1>Mon Panier</h1>
    <a href="index.php">Retour à la boutique</a>

    <?php if ($cart && !empty($cart['items'])): ?>
        <table>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix total</th>
            </tr>
            <?php foreach ($cart['items'] as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?> €</td>
                    <td><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?> €</td>
                </tr>
                <?php $totalPrice += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>
        </table>
        <h3>Prix total : <?php echo $totalPrice; ?> €</h3>
        <form action="save_cart.php" method="POST">
            <button type="submit">Enregistrer le panier</button>
        </form>
    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</body>
</html>
