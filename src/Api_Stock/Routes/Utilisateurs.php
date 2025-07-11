<?php
header('Content-Type: application/json');
require_once("./Controlleurs/Utilisateurs.php");

// Récupération du chemin de la requête
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = explode('/', trim($request_uri, '/'));

// Recherche la position de "utilisateur" dans l'URL
$key = array_search('utilisateur', $url);
$url_path1 = $url[$key] ?? null;
$url_path2 = $url[$key + 1] ?? null;

if ($url_path1 === "utilisateur") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if ($url_path2 === "liste") {
            selectionner_utilisateurs();
            exit;
        } elseif ($url_path2 === "details") {
            selectionner_utilisateur();
            exit;
        } elseif ($url_path2 === "rechercher") {
            rechercher_utilisateurs();
            exit;
        }
    }
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($url_path2 === "ajouter") {
            ajouter_utilisateur();
            exit;
        } elseif ($url_path2 === "modifier") {
            modifier_utilisateur();
            exit;
        } elseif ($url_path2 === "supprimer") {
            supprimer_utilisateur();
            exit;
        } elseif ($url_path2 === "connexion") {
            connexion_utilisateur();
            exit;
        }
    }
}

// Si aucune route ne correspond
echo json_encode([
    "succes" => false,
    "message" => "Route non trouvée"
]);
exit;