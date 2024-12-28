<?php 
session_start();
require 'db_connect.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Récupérer le nom de l'utilisateur connecté
$username = htmlspecialchars($_SESSION['username']);

// Connexion à MongoDB
$db = getMongoDBConnection();
$productsCollection = $db->products;

// Récupération des produits
$products = $productsCollection->find();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez AMMU-NATION <?= $username ?></title>
    <link rel="stylesheet" href="styleindex.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <h1>Bienvenue chez AMMU-NATION <?= $username ?></h1>
        <nav>
            <a href="cart.php">Mon panier</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>
    
    <main>
        <h2>Nos produits</h2>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <!-- Vérification et affichage de l'image -->
                    <?php if (!empty($product['image'])): ?>
                        <img 
                            src="image/<?= htmlspecialchars($product['image']) ?>" 
                            alt="<?= htmlspecialchars($product['name']) ?>" 
                            class="product-image"
                            width="200" 
                            height="200"
                        >
                    <?php else: ?>
                        <img 
                            src="image/default-image.jpg" 
                            alt="Image non disponible" 
                            class="product-image"
                            width="200" 
                            height="200"
                        >
                    <?php endif; ?>
                    
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p>Prix : <?= htmlspecialchars($product['price']) ?> €</p>
                    <p id="stock-<?= (string)$product['_id'] ?>">Stock : <?= htmlspecialchars($product['stock']) ?></p>
                    <button 
                        class="add-to-cart" 
                        data-id="<?= (string)$product['_id'] ?>"
                        <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                        <?= $product['stock'] < 1 ? 'Rupture de stock' : 'Ajouter au panier' ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Online AMMU-NATION. All rights reserved.</p>
    </footer>

    <script>
        $(document).ready(function() {
            // Gestion de l'ajout au panier
            $(".add-to-cart").on("click", function() {
                const productId = $(this).data("id");

                $.ajax({
                    url: "add_to_cart.php",
                    type: "POST",
                    data: { product_id: productId },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                $(`#stock-${productId}`).text("Stock : " + data.new_stock);
                                alert("Produit ajouté au panier avec succès !");
                            } else {
                                alert("Erreur : " + data.message);
                            }
                        } catch (e) {
                            alert("Une erreur s'est produite lors du traitement de la réponse.");
                        }
                    },
                    error: function() {
                        alert("Une erreur s'est produite lors de l'ajout au panier.");
                    }
                });
            });
        });
    </script>
</body>
</html>