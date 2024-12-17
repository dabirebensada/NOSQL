<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = floatval($_POST["price"]);

    // Connexion à MongoDB
    $db = getMongoDBConnection();
    $productsCollection = $db->products;

    // Mise à jour du produit
    $update = [
        '$set' => [
            "name" => $name,
            "description" => $description,
            "price" => $price
        ]
    ];

    $productsCollection->updateOne(
        ["_id" => new MongoDB\BSON\ObjectId($id)],
        $update
    );

    // Redirection vers la page admin
    header("Location: ../views/admin.php");
    exit();
}
?>