<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../db_connect.php';

class DbConnectTest extends TestCase
{
    public function testMongoDBConnection()
    {
        $db = getMongoDBConnection();
        $this->assertInstanceOf(MongoDB\Database::class, $db, "La connexion MongoDB n'a pas retourné une instance valide.");
    }
}
