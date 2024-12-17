<?php 
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $image = $_POST["image"];
    $created_at = new MongoDB\BSON\UTCDateTime(); // Date actuelle au format BSON

    // Connexion à la base MongoDB
    $db = getMongoDBConnection();
    $productsCollection = $db->products;

    // Création du produit
    $newProduct = [
        "name" => $name,
        "description" => $description,
        "price" => $price,
        "stock" => $stock,
        "image" => $image,
        "created_at" => $created_at
    ];

    // Insertion dans MongoDB
    $productsCollection->insertOne($newProduct);

    // Redirection vers la page admin
    header("Location: admin.php");
    exit();
}
?>
