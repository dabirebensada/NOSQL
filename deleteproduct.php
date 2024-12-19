<<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $db = getMongoDBConnection();
        $productsCollection = $db->products;

        $deleteResult = $productsCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($id)]);

        if ($deleteResult->getDeletedCount() === 1) {
            header("Location: admin.php?status=deleted");
        } else {
            echo "Erreur lors de la suppression.";
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>