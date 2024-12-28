<?php 
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter un produit au panier.']);
        exit();
    }

    $username = $_SESSION['username'];

    $db = getMongoDBConnection();
    $productsCollection = $db->products;
    $cartCollection = $db->cart;

    try {
        $productObjectId = new MongoDB\BSON\ObjectId($productId);
        $product = $productsCollection->findOne(['_id' => $productObjectId]);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produit introuvable.']);
            exit();
        }

        $currentStock = $product['stock'] ?? 0;
        if ($currentStock < 1) {
            echo json_encode(['success' => false, 'message' => 'Stock insuffisant pour ce produit.']);
            exit();
        }

        $newStock = $currentStock - 1;
        $productsCollection->updateOne(
            ['_id' => $productObjectId],
            ['$set' => ['stock' => $newStock]]
        );

        $existingCart = $cartCollection->findOne(['username' => $username]);

        if ($existingCart) {
            $cartItems = $existingCart['products'] ?? [];
            $productFound = false;

            foreach ($cartItems as &$item) {
                if ((string)$item['_id'] === $productId) {
                    $item['quantity'] += 1;
                    $productFound = true;
                    break;
                }
            }

            if (!$productFound) {
                $cartItems[] = [
                    '_id' => $productObjectId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                ];
            }

            $cartCollection->updateOne(
                ['_id' => $existingCart['_id']],
                ['$set' => ['products' => $cartItems]]
            );
        } else {
            $cartCollection->insertOne([
                'username' => $username,
                'products' => [
                    [
                        '_id' => $productObjectId,
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => 1,
                    ],
                ],
                'created_at' => new MongoDB\BSON\UTCDateTime(),
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier avec succès.', 'new_stock' => $newStock]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
exit();
?>
