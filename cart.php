<?php 
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$db = getMongoDBConnection();
$cartCollection = $db->cart;

$username = $_SESSION['username'];
$cart = $cartCollection->findOne(['username' => $username]);
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - AMMU-NATION</title>
    <link rel="stylesheet" href="stylecart.css">
</head>
<body>
    <h1>Mon Panier</h1>
    <a href="index.php">Retour à la boutique</a>
    
    <?php if ($cart && isset($cart['products']) && count($cart['products']) > 0): ?>
        <table>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix total</th>
            </tr>
            <?php foreach ($cart['products'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']); ?></td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td><?= htmlspecialchars($item['price']); ?> €</td>
                    <td><?= htmlspecialchars($item['price'] * $item['quantity']); ?> €</td>
                </tr>
                <?php $totalPrice += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>
        </table>
        <h3>Prix total : <?= $totalPrice; ?> €</h3>
        <form action="save_cart.php" method="POST">
            <button type="submit">Enregistrer le panier</button>
        </form>
    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</body>
</html>
