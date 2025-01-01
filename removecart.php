<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer un produit du panier.']);
        exit();
    }

    $username = $_SESSION['username'];

    $db = getMongoDBConnection();
    $productsCollection = $db->products;
    $cartCollection = $db->cart;

    try {
        $productObjectId = new MongoDB\BSON\ObjectId($productId);

        // Trouver le panier de l'utilisateur
        $cart = $cartCollection->findOne(['username' => $username]);
        if (!$cart || !isset($cart['products'])) {
            echo json_encode(['success' => false, 'message' => 'Panier introuvable.']);
            exit();
        }

        $cartItems = $cart['products'];
        $updatedCartItems = [];
        $productFound = false;

        foreach ($cartItems as $item) {
            if ((string)$item['_id'] === $productId) {
                $productFound = true;
                
                // Mise à jour du stock
                $productsCollection->updateOne(
                    ['_id' => $productObjectId],
                    ['$inc' => ['stock' => $item['quantity']]]
                );
            } else {
                $updatedCartItems[] = $item;
            }
        }

        if (!$productFound) {
            echo json_encode(['success' => false, 'message' => 'Produit introuvable dans le panier.']);
            exit();
        }

        // Mettre à jour le panier
        $cartCollection->updateOne(
            ['_id' => $cart['_id']],
            ['$set' => ['products' => $updatedCartItems]]
        );

        echo json_encode(['success' => true, 'message' => 'Produit supprimé du panier avec succès.']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
exit();