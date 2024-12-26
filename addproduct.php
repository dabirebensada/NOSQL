<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = isset($_POST["price"]) ? floatval($_POST["price"]) : 0;
    $stock = isset($_POST["stock"]) ? intval($_POST["stock"]) : 0;
    $image = trim($_POST["image"]);

    if (empty($name) || empty($description) || $price <= 0 || $stock < 0) {
        echo "Erreur : Veuillez remplir tous les champs correctement.";
        exit();
    }

    $created_at = new MongoDB\BSON\UTCDateTime();

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
