<?php
require_once("./Models/Approvisionnement.php");

function save_approvisionnement() {
    header('Content-Type: application/json');
    $id_user = isset($_POST["id_User"]) ? (int)$_POST["id_User"] : null;
    $id_produit = isset($_POST["id_produit"]) ? (int)$_POST["id_produit"] : null;
    $id_fournisseur = isset($_POST["id_fournisseur"]) ? (int)$_POST["id_fournisseur"] : null;
    $quantite = isset($_POST["quantite"]) ? (float)$_POST["quantite"] : null;
    $prix_unitaire = isset($_POST["prix_unitaire"]) ? (float)$_POST["prix_unitaire"] : null;

    if (!$id_user || !$id_produit || !$id_fournisseur || !$quantite || !$prix_unitaire) {
        echo json_encode([
            "succes" => false,
            "message" => "Tous les champs sont obligatoires"
        ]);
        return;
    }

    $result = Approvisionnement::save($id_user, $id_produit, $id_fournisseur, $quantite, $prix_unitaire);

    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"],
            "data" => $result["Dernier_Enregistrement"] ?? null
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ]);
    }
}

function delete_approvisionnement() {
    header('Content-Type: application/json');
    $id_approvisionnement = isset($_POST["id_approvisionnement"]) ? (int)$_POST["id_approvisionnement"] : null;

    if (!$id_approvisionnement) {
        echo json_encode([
            "succes" => false,
            "message" => "ID approvisionnement manquant"
        ]);
        return;
    }

    $result = Approvisionnement::delete($id_approvisionnement);
    if (isset($result["Reussite"])) {
        echo json_encode([
            "succes" => true,
            "message" => $result["Reussite"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Erreur inconnue"
        ]);
    }
}

function update_approvisionnement() {
    header('Content-Type: application/json');
    $id_approvisionnement = isset($_POST["id_approvisionnement"]) ? (int)$_POST["id_approvisionnement"] : null;
    $id_user = isset($_POST["id_User"]) ? (int)$_POST["id_User"] : null;
    $id_produit = isset($_POST["id_produit"]) ? (int)$_POST["id_produit"] : null;
    $id_fournisseur = isset($_POST["id_fournisseur"]) ? (int)$_POST["id_fournisseur"] : null;
    $quantite = isset($_POST["quantite"]) ? (float)$_POST["quantite"] : null;
    $prix_unitaire = isset($_POST["prix_unitaire"]) ? (float)$_POST["prix_unitaire"] : null;

    if (!$id_approvisionnement || !$id_user || !$id_produit || !$id_fournisseur || !$quantite || !$prix_unitaire) {
        echo json_encode([
            "succes" => false,
            "message" => "Tous les champs sont obligatoires pour la modification"
        ]);
        return;
    }

    $result = Approvisionnement::update($id_approvisionnement, $id_user, $id_produit, $id_fournisseur, $quantite, $prix_unitaire);
    if (isset($result["status"]) && $result["status"] === "success") {
        echo json_encode([
            "succes" => true,
            "message" => $result["message"],
            "date_mise_a_jour" => $result["date_mise_a_jour"] ?? null
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["message"] ?? "Erreur inconnue"
        ]);
    }
}

function select_all_approvisionnements() {
    header('Content-Type: application/json');
    $result = Approvisionnement::select_all();
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun approvisionnement trouvé"
        ]);
    }
}

function select_one_approvisionnement() {
    header('Content-Type: application/json');
    $id_approvisionnement = isset($_GET["id_approvisionnement"]) ? (int)$_GET["id_approvisionnement"] : null;

    if (!$id_approvisionnement) {
        echo json_encode([
            "succes" => false,
            "message" => "ID approvisionnement manquant"
        ]);
        return;
    }

    $result = Approvisionnement::select_one($id_approvisionnement);
    if (isset($result["id_approvisionnement"])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun approvisionnement trouvé"
        ]);
    }
}

function count_approvisionnements() {
   header('Content-Type: application/json; charset=utf-8');
    $result = Approvisionnement::count();
    
    if (isset($result["Message"])) {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"]
        ]);
    } else {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    }
    exit;
}

function search_approvisionnements() {
    header('Content-Type: application/json');
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;

    if (!$search_term) {
        echo json_encode([
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ]);
        return;
    }

    $result = Approvisionnement::search($search_term);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun approvisionnement trouvé"
        ]);
    }
}