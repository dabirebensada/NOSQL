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
$ordersCollection = $db->orders;

$username = $_SESSION['username'];

// Récupérer le panier
$cart = $cartCollection->findOne(['username' => $username]);

if ($cart && !empty($cart['items'])) {
    // Enregistrer le panier comme une commande
    $ordersCollection->insertOne([
        'username' => $username,
        'items' => $cart['items'],
        'total_price' => array_reduce($cart['items'], function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0),
        'created_at' => new MongoDB\BSON\UTCDateTime(),
    ]);

    // Supprimer le panier
    $cartCollection->deleteOne(['_id' => $cart['_id']]);

    header("Location: confirmation.php");
    exit();
}

header("Location: cart.php");
exit();
