<?php
require_once("./Models/Produit.php");

header('Content-Type: application/json; charset=utf-8');

function enregistrer_produit()
{
    $designation = isset($_POST["designation"]) ? htmlspecialchars(trim($_POST["designation"])) : null;
    $description = isset($_POST["description"]) ? htmlspecialchars(trim($_POST["description"])) : null;

    if (!$designation) {
        echo json_encode([
            "succes" => false,
            "message" => "La désignation est obligatoire"
        ]);
        return;
    }

    $result = Produit::enregistrer($designation, $description);
    
    if (isset($result["success"]) && $result["success"]) {
        echo json_encode([
            "succes" => true,
            "message" => $result["message"],
            "data" => $result["id"] ?? null
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["message"] ?? "Erreur inconnue"
        ]);
    }
}

function modifier_produit()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    $designation = isset($_POST["designation"]) ? htmlspecialchars(trim($_POST["designation"])) : null;
    $description = isset($_POST["description"]) ? htmlspecialchars(trim($_POST["description"])) : null;

    if (!$id || !$designation) {
        echo json_encode([
            "succes" => false,
            "message" => "ID et désignation sont obligatoires"
        ]);
        return;
    }

    $result = Produit::update($id, $designation, $description);
    
    if (isset($result["success"]) && $result["success"]) {
        echo json_encode([
            "succes" => true,
            "message" => $result["message"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["message"] ?? "Erreur inconnue"
        ]);
    }
}

function selectionner_produits()
{
    $result = Produit::select_all();
    
    if (!empty($result)) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => "Aucun produit trouvé"
        ]);
    }
}

function selectionner_un_produit()
{
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : null;
    
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "ID produit manquant"
        ]);
        return;
    }

    $result = Produit::select_one($id);
    
    if ($result) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => "Produit non trouvé"
        ]);
    }
}

function compter_produits()
{
    $result = Produit::count();
    
    echo json_encode([
        "succes" => true,
        "total" => $result
    ]);
}

function supprimer_produit()
{
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "ID produit manquant"
        ]);
        return;
    }

    $result = Produit::delete($id);
    
    if (isset($result["success"]) && $result["success"]) {
        echo json_encode([
            "succes" => true,
            "message" => $result["message"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["message"] ?? "Erreur inconnue"
        ]);
    }
}

function rechercher_produits()
{
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;
    
    if (!$search_term) {
        echo json_encode([
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ]);
        return;
    }

    $result = Produit::search($search_term);
    
    if (!empty($result)) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => "Aucun résultat trouvé"
        ]);
    }
}


// Gestion des requêtes
