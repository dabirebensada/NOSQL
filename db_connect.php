<?php
require 'vendor/autoload.php'; // Charge Composer pour MongoDB

function getMongoDBConnection() {
    try {
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $database = $client->armurerie; // Remplacez 'armurerie' par le nom de votre base
        return $database;
    } catch (Exception $e) {
        die("Erreur de connexion à MongoDB : " . $e->getMessage());
    }
}
?>