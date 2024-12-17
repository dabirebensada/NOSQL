<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];

    // Connexion à MongoDB
    $db = getMongoDBConnection();
    $productsCollection = $db->products;

    // Suppression du produit
    $productsCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($id)]);

    // Redirection vers la page admin
    header("Location: ../views/admin.php");
    exit();
}
?>