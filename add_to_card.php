<?php
session_start();
require 'db_connect.php';

// Vérification des données envoyées par AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    $db = getMongoDBConnection();
    $productsCollection = $db->products;
    $cartCollection = $db->cart;

    // Recherche du produit par ID
    $product = $productsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produit introuvable.']);
        exit();
    }

    // Vérification du stock
    $currentStock = $product['stock'] ?? 0;
    if ($currentStock < 1) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant.']);
        exit();
    }

    // Mise à jour du stock
    $newStock = $currentStock - 1;
    $productsCollection->updateOne(
        ['_id' => $product['_id']],
        ['$set' => ['stock' => $newStock]]
    );

    // Ajout du produit au panier de l'utilisateur
    $username = $_SESSION['username'];

    // Vérifier si l'utilisateur a déjà un panier
    $existingCart = $cartCollection->findOne(['username' => $username]);

    if ($existingCart) {
        // Mise à jour du panier existant
        $cartItems = $existingCart['items'];

        $found = false;
        foreach ($cartItems as &$item) {
            if ((string)$item['_id'] === $productId) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cartItems[] = [
                '_id' => $product['_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
            ];
        }

        $cartCollection->updateOne(
            ['_id' => $existingCart['_id']],
            ['$set' => ['items' => $cartItems]]
        );
    } else {
        // Création d'un nouveau panier
        $cartCollection->insertOne([
            'username' => $username,
            'items' => [
                [
                    '_id' => $product['_id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                ],
            ],
            'created_at' => new MongoDB\BSON\UTCDateTime(),
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier.', 'new_stock' => $newStock]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
exit();

