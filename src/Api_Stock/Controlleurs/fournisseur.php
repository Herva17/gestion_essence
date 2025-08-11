<?php
require_once("./Models/Fournisseur.php");

// Fonction pour enregistrer un fournisseur
function save_fournisseur()
{
    header('Content-Type: application/json');
    $nom_fournisseur = isset($_POST["nom_fournisseur"]) ? htmlspecialchars(trim($_POST["nom_fournisseur"])) : null;
    $adresse = isset($_POST["adresse"]) ? htmlspecialchars(trim($_POST["adresse"])) : null;
    $telephone = isset($_POST["telephone"]) ? htmlspecialchars(trim($_POST["telephone"])) : null;
    $email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : null;

    if (!$nom_fournisseur || !$telephone || !$email) {
        echo json_encode([
            "succes" => false,
            "message" => "Les champs nom, téléphone et email sont obligatoires"
        ]);
        return;
    }

    $result = Fournisseur::save($nom_fournisseur, $adresse, $telephone, $email);

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

// Fonction pour supprimer un fournisseur
function delete_fournisseur()
{
    header('Content-Type: application/json');
    $id_fournisseur = isset($_POST["id_fournisseur"]) ? htmlspecialchars(trim($_POST["id_fournisseur"])) : null;

    if (!$id_fournisseur) {
        echo json_encode([
            "succes" => false,
            "message" => "ID fournisseur manquant"
        ]);
        return;
    }

    $result = Fournisseur::delete($id_fournisseur);
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

// Fonction pour modifier un fournisseur
function update_fournisseur()
{
    header('Content-Type: application/json');
    $id_fournisseur = isset($_POST["id_fournisseur"]) ? htmlspecialchars(trim($_POST["id_fournisseur"])) : null;
    $nom_fournisseur = isset($_POST["nom_fournisseur"]) ? htmlspecialchars(trim($_POST["nom_fournisseur"])) : null;
    $adresse = isset($_POST["adresse"]) ? htmlspecialchars(trim($_POST["adresse"])) : null;
    $telephone = isset($_POST["telephone"]) ? htmlspecialchars(trim($_POST["telephone"])) : null;
    $email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : null;

    if (!$id_fournisseur || !$nom_fournisseur || !$telephone || !$email) {
        echo json_encode([
            "succes" => false,
            "message" => "Tous les champs sont obligatoires pour la modification"
        ]);
        return;
    }

    $result = Fournisseur::update($id_fournisseur, $nom_fournisseur, $adresse, $telephone, $email);
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

// Fonction pour récupérer tous les fournisseurs
function select_all_fournisseurs()
{
    header('Content-Type: application/json');
    $result = Fournisseur::select_all();
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun fournisseur trouvé"
        ]);
    }
}

// Fonction pour récupérer un fournisseur spécifique
function select_one_fournisseur()
{
    header('Content-Type: application/json');
    $id_fournisseur = isset($_GET["id_fournisseur"]) ? htmlspecialchars(trim($_GET["id_fournisseur"])) : null;

    if (!$id_fournisseur) {
        echo json_encode([
            "succes" => false,
            "message" => "ID fournisseur manquant"
        ]);
        return;
    }

    $result = Fournisseur::select_one($id_fournisseur);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result[0]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun fournisseur trouvé"
        ]);
    }
}

// Fonction pour compter les fournisseurs
function count_fournisseurs()
{
    header('Content-Type: application/json');
    $result = Fournisseur::count_fournisseurs();
    if (isset($result[0]["total"])) {
        echo json_encode([
            "succes" => true,
            "total" => $result[0]["total"]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucune donnée disponible"
        ]);
    }
}

// Fonction pour rechercher des fournisseurs
function search_fournisseurs()
{
    header('Content-Type: application/json');
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;

    if (!$search_term) {
        echo json_encode([
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ]);
        return;
    }

    $result = Fournisseur::search($search_term);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun fournisseur trouvé"
        ]);
    }
}