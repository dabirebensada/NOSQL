<?php 
require 'db_connect.php';

// Vérification de l'existence de l'ID du produit dans l'URL
if (!isset($_GET['id'])) {
    header("Location: admin.php"); 
    exit();
}

$id = $_GET['id']; 


$db = getMongoDBConnection();
$productsCollection = $db->products;

// Recherche du produit par ID
$product = $productsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($id)]);

if (!$product) {
    echo "Produit non trouvé";
    exit();
}

$name = $product['name'] ?? '';
$description = $product['description'] ?? '';
$price = $product['price'] ?? 0;
$stock = $product['stock'] ?? 0;
$image = $product['image'] ?? '';

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $image = $_POST["image"];

    // Mise à jour du produit dans MongoDB
    try {
        $update = [
            '$set' => [
                "name" => $name,
                "description" => $description,
                "price" => $price,
                "stock" => $stock,
                "image" => $image
            ]
        ];

        $result = $productsCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($id)],
            $update
        );

        if ($result->getModifiedCount() > 0) {
            // Redirection vers la page admin après la mise à jour
            header("Location: admin.php");
            exit();
        } else {
            echo "Aucune modification appliquée.";
        }
    } catch (Exception $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le produit - AMUNATION</title>
    <link rel="stylesheet" href="styleupdate.css">
</head>
<body>
    <h1>Modifier le produit</h1>
    <form action="updateproduct.php?id=<?php echo htmlspecialchars($id); ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div>
            <label for="name">Nom du produit :</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div>
            <label for="price">Prix :</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" step="0.01" required>
        </div>
        <div>
            <label for="stock">Stock :</label>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($stock); ?>" required>
        </div>
        <div>
            <label for="image">Image (nom du fichier) :</label>
            <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($image); ?>" required>
        </div>
        <button type="submit">Mettre à jour le produit</button>
    </form>

    <a href="admin.php">Retour à l'espace admin</a>
</body>
</html>