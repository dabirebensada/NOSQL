<?php

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        // connexion à la base de données
        $this->db = getMongoDBConnection();
    }

    // Test de la connexion à la base de données
    public function testDatabaseConnection()
    {
        $this->assertNotNull($this->db, "La connexion à la base de données doit être établie.");
    }

    // Test des produits dans la base
    public function testProductsExist()
    {
        $productsCollection = $this->db->products;
        $products = $productsCollection->find()->toArray();

        $this->assertNotEmpty($products, "La collection de produits ne doit pas être vide.");
    }

    // Test d'ajout au panier
    public function testAddToCart()
    {
        $cartCollection = $this->db->cart;

        // Simuler un utilisateur
        $username = "testuser";
        $cartCollection->insertOne([
            'username' => $username,
            'products' => [],
        ]);

        $cart = $cartCollection->findOne(['username' => $username]);
        $this->assertNotNull($cart, "Le panier de l'utilisateur doit être créé.");
    }

    // Test de suppression d'un article du panier
    public function testRemoveFromCart()
    {
        $cartCollection = $this->db->cart;

        // Ajouter un produit au panier
        $username = "testuser";
        $cartCollection->updateOne(
            ['username' => $username],
            ['$set' => ['products' => [['name' => 'Test Product', 'quantity' => 1]]]]
        );

        // Vérifier que le produit a bien été ajouté
        $cart = $cartCollection->findOne(['username' => $username]);
        $this->assertCount(1, $cart['products'], "Le panier doit contenir un produit.");

        // Supprimer le produit
        $cartCollection->updateOne(
            ['username' => $username],
            ['$set' => ['products' => []]]
        );

        // Vérifier que le panier est vide
        $cart = $cartCollection->findOne(['username' => $username]);
        $this->assertEmpty($cart['products'], "Le panier doit être vide après suppression.");
    }
}
