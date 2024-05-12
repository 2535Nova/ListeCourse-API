<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
error_reporting(E_ALL);
// Fonction pour établir une connexion à la base de données MySQL
function get_db_connection()
{
    $host = '192.168.153.10';
    $username = 'gpetit';
    $password = 'btssio';
    $dbname = '202324_courses_gpetit';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

// Analyser l'URL pour déterminer l'action à effectuer
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Exécuter l'action appropriée en fonction de l'URL
switch ($action) {
    case 'connexion_utilisateur':
        // Vérifier les paramètres requis
        if (isset($_GET['pseudo']) && isset($_GET['password'])) {
            // Appeler la fonction de connexion utilisateur avec les paramètres
            echo connexion_utilisateur($_GET['pseudo'], $_GET['password']);

        } else {
            echo json_encode(['error' => 'Paramètres manquants pour la connexion utilisateur']);
        }
        break;

    case 'rayons':
        // Appeler la fonction pour obtenir la liste des rayons
        echo get_rayons();
        break;
    case 'resetter':
        // Appeler la fonction pour réinitialiser la liste de courses et du caddie
        echo resetter();
        break;
    case 'ajouter':
        // Analyser l'entrée JSON du corps de la requête
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        // Vérifier si les données JSON ont été correctement décodées et si elles contiennent les clés nécessaires
        if ($input !== null && isset($input['nom']) && isset($input['commentaire']) && isset($input['createur_id']) && isset($input['rayon_id'])) {
            // Créer un tableau contenant les données du produit à ajouter
            $produit = [
                'nom' => $input['nom'],
                'commentaire' => $input['commentaire'],
                'createur_id' => $input['createur_id'],
                'rayon_id' => $input['rayon_id']
            ];
            // Appeler la fonction pour ajouter le produit avec les paramètres
            echo ajouter_produit($produit);
        } else {
            echo json_encode(['error' => 'Données JSON incomplètes pour l\'ajout de produit']);
        }

        break;
    case 'courses':
        // Appeler la fonction pour obtenir la liste de courses
        echo get_courses();
        break;
    case 'caddie':
        // Appeler la fonction pour obtenir la liste du caddie
        echo get_caddie();
        break;
    case 'transferer':
        // Vérifier les paramètres requis
        if (isset($_GET['idProduit'])) {
            // Appeler la fonction pour transférer le produit avec l'identifiant spécifié
            echo transferer_produit($_GET['idProduit']);
        } else {
            echo json_encode(['error' => 'Paramètre manquant pour le transfert de produit']);
        }
        break;
    case 'deconnexion':
        // Appeler la fonction pour déconnecter l'utilisateur
        echo deconnexion();
        break;

    default:
        echo json_encode(['error' => 'Action non reconnue']);
}

// Fonction pour connecter un utilisateur
function connexion_utilisateur($pseudo, $password)
{

    $conn = get_db_connection();
    $query = "SELECT * FROM Utilisateur WHERE nom = ? AND mdp_hash = SHA2(?, 256)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pseudo, $password]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
    $conn = null;

    if ($utilisateur) {
        return json_encode(['request' => 'connexion', 'result' => true]);
    } else {
        return json_encode(['request' => 'connexion', 'result' => false]);
    }
}

// Fonction pour obtenir la liste des rayons
function get_rayons()
{
    $conn = get_db_connection();
    $query = "SELECT * FROM Rayon";
    $stmt = $conn->query($query);
    $rayons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $conn = null;
    return json_encode(['request' => 'rayons', 'result' => $rayons]);
}

// Fonction pour réinitialiser la liste de courses et du caddie
function resetter()
{
    // Vous pouvez ajouter la logique de réinitialisation ici si nécessaire
    return json_encode(['request' => 'reset', 'result' => true]);
}

// Fonction pour ajouter un produit dans la liste de courses
function ajouter_produit($produit)
{
    $conn = get_db_connection();
    $query = "INSERT INTO Produit (nom, commentaire, createur_id, rayon_id) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$produit['nom'], $produit['commentaire'], $produit['createur_id'], $produit['rayon_id']]);
    $conn = null;
    return json_encode(['request' => 'ajouter', 'result' => true]);
}

// Fonction pour obtenir la liste de courses
function get_courses()
{
    $conn = get_db_connection();
    $query = "SELECT * FROM Produit WHERE is_caddie = FALSE";
    $stmt = $conn->query($query);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $conn = null;
    return json_encode(['request' => 'courses', 'result' => $courses]);
}

// Fonction pour obtenir la liste du caddie
function get_caddie()
{
    $conn = get_db_connection();
    $query = "SELECT * FROM Produit WHERE is_caddie = TRUE";
    $stmt = $conn->query($query);
    $caddie = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $conn = null;
    return json_encode(['request' => 'caddie', 'result' => $caddie]);
}

// Fonction pour transférer un produit de la liste dans le caddie
function transferer_produit($idProduit)
{
    $conn = get_db_connection();
    $query = "UPDATE Produit SET is_caddie = TRUE WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$idProduit]);
    $conn = null;
    return json_encode(['request' => 'transferer', 'result' => true]);
}

// Fonction pour déconnecter la session utilisateur
function deconnexion()
{
    // Vous pouvez ajouter la logique de déconnexion ici si nécessaire
    return json_encode(['request' => 'deconnexion', 'result' => true]);
}
