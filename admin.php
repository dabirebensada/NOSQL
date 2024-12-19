<?php  
session_start();
require 'db_connect.php'; 

// Vérification de la session et des droits d'administrateur
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = getMongoDBConnection(); 
$productsCollection = $db->products;
$products = $productsCollection->find(); 

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - AMUNATION</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Bienvenue dans l'espace admin de l'AMUNATION</h1>
    <p>Connecté en tant que <?php echo htmlspecialchars($_SESSION['username']); ?></p>

    
    <h2>Produits</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Date de création</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['description']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?> €</td>
            <td><?php echo htmlspecialchars($product['stock']); ?></td>
            <td><?php echo htmlspecialchars($product['image']); ?></td>
            <td><?php echo htmlspecialchars($product['created_at']->toDateTime()->format('Y-m-d H:i:s')); ?></td>
            <td>
                <a href="updateproduct.php?id=<?php echo $product['_id']; ?>">Modifier</a>
                <!-- Formulaire de suppression -->
                <form action="deleteproduct.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $product['_id']; ?>">
                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Formulaire pour ajouter un produit -->
    <h2>Ajouter un produit</h2>
    <form action="addproduct.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Nom du produit :</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div>
            <label for="price">Prix :</label>
            <input type="number" id="price" name="price" step="0.01" required>
        </div>
        <div>
            <label for="stock">Stock :</label>
            <input type="number" id="stock" name="stock" required>
        </div>
        <div>
            <label for="image">Image (nom du fichier) :</label>
            <input type="text" id="image" name="image" required>
        </div>
        <button type="submit">Ajouter le produit</button>
    </form>

    <a href="logout.php">Déconnexion</a>
</body>
</html>