<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$db = getMongoDBConnection();
$cartCollection = $db->cart;
$ordersCollection = $db->orders;

$userId = $_SESSION['username'];

// Récupérer le panier
$cart = $cartCollection->findOne(['username' => $userId]);

if ($cart && !empty($cart['products'])) {
    try {
        // Convertir BSONArray en tableau PHP
        $products = (array) $cart['products'];
        $products = array_map(fn($item) => (array) $item, $products);

        // Calculer le prix total
        $totalPrice = array_reduce($products, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);

        // Insérer les données dans la collection orders
        $ordersCollection->insertOne([
            'user_id' => $userId,
            'products' => $products,
            'total_price' => $totalPrice,
            'status' => 'validated',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        // Supprimer le panier
        $cartCollection->deleteOne(['_id' => $cart['_id']]);

        // Rediriger vers la page de confirmation
        header("Location: confirmation.php");
        exit();
    } catch (Exception $e) {
        echo "Erreur lors de l'enregistrement de la commande : " . $e->getMessage();
        exit();
    }
}

header("Location: cart.php");
exit();
