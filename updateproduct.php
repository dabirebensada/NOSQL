<?php
require 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = $_GET['id'];

try {
    $db = getMongoDBConnection();
    $productsCollection = $db->products;

    $product = $productsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($id)]);
    if (!$product) {
        echo "Produit non trouvé";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST["name"] ?? '';
        $description = $_POST["description"] ?? '';
        $price = floatval($_POST["price"] ?? 0);
        $stock = intval($_POST["stock"] ?? 0);
        $image = $_POST["image"] ?? '';

        $updateResult = $productsCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($id)],
            ['$set' => compact('name', 'description', 'price', 'stock', 'image')]
        );

        if ($updateResult->getModifiedCount() > 0) {
            header("Location: admin.php?status=updated");
        } else {
            echo "Aucune modification détectée.";
        }
        exit();
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>