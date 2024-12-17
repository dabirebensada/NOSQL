<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = floatval($_POST["price"]);

    // Connexion à la base MongoDB
    $db = getMongoDBConnection();
    $productsCollection = $db->products;

    // Création du produit
    $newProduct = [
        "name" => $name,
        "description" => $description,
        "price" => $price
    ];

    // Insertion dans MongoDB
    $productsCollection->insertOne($newProduct);

    // Redirection vers la page admin
    header("Location: ../views/admin.php");
    exit();
}
?>
