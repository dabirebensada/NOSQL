<?php
require 'db_connect.php'; // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validation du mot de passe
    if ($password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    }

    // Vérification que le mot de passe est sécurisé
    if (strlen($password) < 6) {
        echo "Le mot de passe doit contenir au moins 6 caractères.";
        exit();
    }

    // Connexion à la base de données
    $db = getMongoDBConnection();
    $usersCollection = $db->users;

    // Vérification si l'utilisateur existe déjà
    $existingUser = $usersCollection->findOne(["username" => $username]);
    if ($existingUser) {
        echo "Le nom d'utilisateur est déjà pris.";
        exit();
    }

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insertion de l'utilisateur dans la base de données
    $newUser = [
        "username" => $username,
        "password" => $hashedPassword,
        "role" => "user" // Par défaut, tous les utilisateurs sont des "user"
    ];
    $usersCollection->insertOne($newUser);

    // Redirection vers la page de connexion après une inscription réussie
    header("Location: login.php");
    exit();
}
?>