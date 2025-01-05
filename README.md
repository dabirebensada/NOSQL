# Rapport du Projet : Boutique en Ligne Armurerie

## Contexte et Utilité du Projet
Le projet consiste en la création d'une boutique en ligne spécialisée dans la vente d'armes fictives, nommée "Armurerie". L’objectif principal est de proposer une expérience utilisateur fluide, avec une gestion claire et efficace des produits, des paniers et des commandes. Le projet est divisé en deux parties principales : une interface utilisateur (clients) et une interface administrateur. Les fonctionnalités sont soutenues par une base de données MongoDB qui assure le stockage et la gestion des informations essentielles.

## Les Collections de la Base de Données
La base de données MongoDB se nomme "armurerie" et contient les collections suivantes :

1. **Products** : Contient les produits disponibles à la vente.
   - **Champs** : _id, name, description, price, stock, image, created_at.
   - **Exemple** : Une arme avec un nom, une description, un prix, une image et des informations de stock.
   
2. **Users** : Contient les informations des administrateurs et des utilisateurs enregistrés.
   - **Champs** : _id, username, password, role (admin ou user).
   - **Exemple** : L’administrateur a des droits spécifiques, tandis que les utilisateurs peuvent acheter des produits.
   
3. **Cart** : Gère les informations des paniers des utilisateurs.
   - **Champs** : _id, username, products.
   - **Exemple** : Un utilisateur ajoute plusieurs produits à son panier avant de passer commande.
   
4. **Orders** : Enregistre les commandes validées.
   - **Champs** : _id, user_id, products, total_price, status, created_at.
   - **Exemple** : Après validation d’un panier, les informations sont enregistrées comme une commande.

## Fonctionnalités du Projet

### 1. Accès et Authentification
- **Page de Connexion (LOGIN.PHP)** :
  - Permet à un utilisateur (admin ou client) de se connecter grâce à un formulaire.
  - Vérification des identifiants via `LOGPROCESS.PHP`.
  - Redirection :
    - Si admin : vers l’espace administrateur.
    - Si utilisateur : vers la boutique principale.
  
- **Création de Compte (REGISTER.PHP)** :
  - Permet aux nouveaux utilisateurs de s’enregistrer via un formulaire.
  - Les informations sont stockées dans la collection Users via `REGISTERPROCESS.PHP`.
  
- **Déconnexion (LOGOUT.PHP)** :
  - Terminaison de la session active et redirection vers la page de connexion.

### 2. Interface Utilisateur (Client)
- **Page Principale (INDEX.PHP)** :
  - Affiche la liste des produits disponibles (récupérés depuis la collection Products).
  - Chaque produit inclut : nom, description, prix, stock restant, et un bouton "Ajouter au panier".
  - Gestion via `ADD_TO_CART.PHP` : Lorsqu’un produit est ajouté, les informations sont mises à jour dans la collection Cart.
  
- **Panier (CART.PHP)** :
  - Affiche les produits ajoutés au panier avec leurs quantités, prix et un total.
  - Gestion des produits :
    - Suppression via `REMOVECART.PHP`.
    - Validation via `SAVE_CART.PHP`, qui transfère les données du panier vers la collection Orders.
  
- **Confirmation de Commande (CONFIRMATION.PHP)** :
  - Affiche un message de validation après le passage de commande.

### 3. Interface Administrateur
- **Tableau de Bord (ADMIN.PHP)** :
  - Liste tous les produits disponibles dans la boutique avec des options pour les gérer.

- **Gestion des Produits** :
  - **Ajout de Produits (ADDPRODUCT.PHP)** :
    - Formulaire permettant d’ajouter un nouveau produit à la collection Products.
  - **Modification de Produits (UPDATEPRODUCT.PHP)** :
    - Permet de mettre à jour les informations d’un produit (stock, description, etc.).
    - Les modifications sont immédiatement reflétées côté utilisateur et admin.
  - **Suppression de Produits (DELETEPRODUCT.PHP)** :
    - Supprime un produit de la base de données, le rendant indisponible partout.

## Processus Techniques

### Gestion des Stocks
- Lorsqu’un utilisateur ajoute un produit au panier, la quantité disponible diminue dynamiquement dans la boutique.
- Si un produit est entièrement vendu :
  - Il est marqué comme "Épuisé".
  - L’administrateur peut recharger le stock depuis son tableau de bord.

### Structure CSS
Chaque page est associée à un fichier CSS spécifique pour un rendu adapté :
- `LOGIN.PHP` : `STYLE.CSS`
- `REGISTER.PHP` : `STYLEREJ.CSS`
- `ADMIN.PHP` : `STYLEADMIN.CSS`
- `INDEX.PHP` : `STYLEINDEX.CSS`
- `CART.PHP` : `STYLECART.CSS`
- `CONFIRMATION.PHP` : `STYLECONFIRM.CSS`
- `UPDATEPRODUCT.PHP` : `STYLEUPDATE.CSS`

## Installation et Lancement

### Prérequis
- **Serveur Web** : WampServer.
- **Base de Données** : MongoDB.
  Après avoir installé MongoDB, exécutez une commande dans le terminal de votre ordinateur pour créer la base de données et les collections.

- **PHP** : Version 7.4 ou supérieure.

### Étapes d’Installation
1. **Clonez le projet** :
   ```bash
   git clone https://github.com/dabirebensada/nosql.git
   cd nosql
   ```
   
2. **Configurez la base de données MongoDB** :
   - Importez les collections initiales (products, users, etc.) dans MongoDB.
   - Vous pouvez utiliser des fichiers JSON ou des commandes d'insertion pour ajouter les données initiales dans MongoDB.

3. **Modifiez le fichier `DB_CONNECT.PHP` avec vos informations de connexion MongoDB** :
   - Ouvrez le fichier `DB_CONNECT.PHP` et configurez-le avec votre URL MongoDB, votre nom d'utilisateur et votre mot de passe si nécessaire.
   - Exemple de connexion à MongoDB :
     ```php
     $client = new MongoDB\Client("mongodb://localhost:27017");
     $db = $client->armurerie;
     ```
     Assurez-vous que la base de données et les collections sont correctement définies dans ce fichier.

4. **Placez les fichiers dans le dossier racine de votre serveur Web** :
   - Si vous utilisez WampServer, déplacez les fichiers du projet dans le répertoire `www` de WampServer.
   - Si vous utilisez XAMPP, placez les fichiers dans le répertoire `htdocs` d'XAMPP.

---

## Lancement du Projet

1. **Démarrez le serveur web** :
   - Ouvrez WampServer ou XAMPP et démarrez les services Apache et MySQL (si nécessaire pour d'autres parties de l'application).

2. **Accédez à l’application via** :
   - Ouvrez votre navigateur web et accédez à l'adresse suivante pour afficher la page de connexion :
     ```
     http://localhost/nosql/login.php
     ```

---

## Lancer les Tests

### Tests Fonctionnels

- **Connexion/Inscription** :
  - Testez la connexion avec différents utilisateurs (admin et client).
  - Vérifiez que la création de compte fonctionne correctement, que les informations sont bien enregistrées dans la base de données MongoDB.
  
- **Boutique Utilisateur** :
  - Testez l'ajout de produits au panier.
  - Vérifiez que vous pouvez supprimer des produits du panier.
  - Testez la validation d'une commande et assurez-vous que les informations sont correctement transférées dans la collection `Orders`.

- **Gestion Admin** :
  - Testez les fonctionnalités d'ajout, de modification et de suppression de produits via l'interface administrateur.
  - Vérifiez que les produits sont bien ajoutés, modifiés ou supprimés dans la collection `Products` de MongoDB.

