<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../save_cart.php';

class SaveCartTest extends TestCase
{
    public function testSaveCartToOrders()
    {
        // Simuler une session utilisateur
        $_SESSION['username'] = 'test_user';

        // Mock d'un panier utilisateur
        $db = getMongoDBConnection();
        $cartCollection = $db->cart;
        $cartCollection->insertOne([
            'username' => 'test_user',
            'products' => [
                ['_id' => new MongoDB\BSON\ObjectId(), 'name' => 'Test Product', 'price' => 10, 'quantity' => 2]
            ]
        ]);

        // Exécuter le script
        ob_start();
        include __DIR__ . '/../save_cart.php';
        ob_get_clean();

        // Vérifier que la commande a bien été enregistrée dans `orders`
        $ordersCollection = $db->orders;
        $order = $ordersCollection->findOne(['user_id' => 'test_user']);

        $this->assertNotNull($order, "La commande n'a pas été enregistrée.");
        $this->assertEquals('validated', $order['status'], "Le statut de la commande est incorrect.");
    }
}
