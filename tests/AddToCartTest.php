<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../add_to_cart.php';

class AddToCartTest extends TestCase
{
    public function testAddProductToCart()
    {
        // Simuler une session utilisateur
        $_SESSION['username'] = 'test_user';

        // Simuler une requête POST
        $_POST['product_id'] = '6445b2d1e9d3a4e9a0c9f5ab'; // Remplacez par un ObjectId valide dans votre base de données

        // Exécuter le script
        ob_start();
        include __DIR__ . '/../add_to_cart.php';
        $output = ob_get_clean();

        // Convertir la sortie JSON en tableau PHP
        $response = json_decode($output, true);

        // Vérifier que le produit a été ajouté avec succès
        $this->assertTrue($response['success'], "Le produit n'a pas été ajouté au panier.");
    }
}
?>
