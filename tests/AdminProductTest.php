<?php

use PHPUnit\Framework\TestCase;

class AdminProductTest extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        // connexion à la base de données
        $this->db = getMongoDBConnection();
    }

    // Test : L'administrateur peut se connecter si 
    public function testAdminLogin()
    {
        $adminUsername = "admin";
        $adminPassword = "azerty123"; 
        $adminsCollection = $this->db->users;

        $adminsCollection->insertOne([
            'username' => $adminUsername,
            'password' => password_hash($adminPassword, PASSWORD_BCRYPT),
        ]);

        $admin = $adminsCollection->findOne(['username' => $adminUsername]);

        $this->assertNotNull($admin, "L'administrateur doit exister dans la base.");
        $this->assertTrue(password_verify($adminPassword, $admin['password']), "Le mot de passe doit correspondre.");
    }

    // Test : Ajout d'un produit
    public function testAddProduct()
    {
        $productsCollection = $this->db->products;

        // Ajouter un produit
        $newProduct = [
            'name' => 'Test Product',
            'description' => 'Description du produit test',
            'price' => 50,
            'stock' => 100,
            'image' => 'test-product.jpg',
        ];

        $productsCollection->insertOne($newProduct);

        // Vérifier que le produit a été ajouté
        $product = $productsCollection->findOne(['name' => 'Test Product']);
        $this->assertNotNull($product, "Le produit doit être ajouté à la base.");
        $this->assertEquals(50, $product['price'], "Le prix doit correspondre.");
    }

    // Test : Modification d'un produit
    public function testUpdateProduct()
    {
        $productsCollection = $this->db->products;

        // Ajouter un produit à modifier
        $productId = $productsCollection->insertOne([
            'name' => 'Test Product',
            'description' => 'Description du produit test',
            'price' => 50,
            'stock' => 100,
        ])->getInsertedId();

        // Modifier le produit
        $productsCollection->updateOne(
            ['_id' => $productId],
            ['$set' => ['price' => 75, 'stock' => 200]]
        );

        // Vérifier les modifications
        $updatedProduct = $productsCollection->findOne(['_id' => $productId]);
        $this->assertEquals(75, $updatedProduct['price'], "Le prix doit être mis à jour.");
        $this->assertEquals(200, $updatedProduct['stock'], "Le stock doit être mis à jour.");
    }

    // Test : Suppression d'un produit
    public function testDeleteProduct()
    {
        $productsCollection = $this->db->products;

        // Ajouter un produit à supprimer
        $productId = $productsCollection->insertOne([
            'name' => 'Test Product to Delete',
            'description' => 'Description du produit test',
            'price' => 50,
            'stock' => 100,
        ])->getInsertedId();

        // Supprimer le produit
        $productsCollection->deleteOne(['_id' => $productId]);

        // Vérifier la suppression
        $deletedProduct = $productsCollection->findOne(['_id' => $productId]);
        $this->assertNull($deletedProduct, "Le produit doit être supprimé.");
    }
}
