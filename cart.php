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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <th>Action</th>
            </tr>
            <?php foreach ($cart['products'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']); ?></td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td><?= htmlspecialchars($item['price']); ?> €</td>
                    <td><?= htmlspecialchars($item['price'] * $item['quantity']); ?> €</td>
                    <td>
                        <button class="removecart" data-id="<?= (string)$item['_id']; ?>">Supprimer</button>
                    </td>
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

    <script>
        $(document).ready(function() {
            $(".removecart").on("click", function() {
                const productId = $(this).data("id");

                $.ajax({
                    url: "removecart.php",
                    type: "POST",
                    data: { product_id: productId },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert("Erreur : " + data.message);
                            }
                        } catch (e) {
                            alert("Une erreur s'est produite lors du traitement de la réponse.");
                        }
                    },
                    error: function() {
                        alert("Une erreur s'est produite lors de la suppression du produit.");
                    }
                });
            });
        });
    </script>
</body>
</html>

