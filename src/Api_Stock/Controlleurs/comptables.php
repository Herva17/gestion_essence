<?php
require_once("./Models/comptables.php");

header('Content-Type: application/json; charset=utf-8');

function enregistrer_comptable()
{
    $nom = isset($_POST["nom"]) ? htmlspecialchars(trim($_POST["nom"])) : null;
    $prenom = isset($_POST["prenom"]) ? htmlspecialchars(trim($_POST["prenom"])) : null;
    $sexe = isset($_POST["sexe"]) ? htmlspecialchars(trim($_POST["sexe"])) : null;
    $adresse = isset($_POST["adresse"]) ? htmlspecialchars(trim($_POST["adresse"])) : null;
    $telephone = isset($_POST["telephone"]) ? htmlspecialchars(trim($_POST["telephone"])) : null;
    $email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : null;

    if (!$nom || !$prenom || !$telephone || !$email) {
        echo json_encode([
            "succes" => false,
            "message" => "Les champs nom, prénom, téléphone et email sont obligatoires"
        ]);
        exit;
    }

    $result = Comptable::enregistrer($nom, $prenom, $sexe, $adresse, $telephone, $email);
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
    exit;
}

function modification_comptable()
{
    $id = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    $nom = isset($_POST["nom"]) ? htmlspecialchars(trim($_POST["nom"])) : null;
    $prenom = isset($_POST["prenom"]) ? htmlspecialchars(trim($_POST["prenom"])) : null;
    $sexe = isset($_POST["sexe"]) ? htmlspecialchars(trim($_POST["sexe"])) : null;
    $adresse = isset($_POST["adresse"]) ? htmlspecialchars(trim($_POST["adresse"])) : null;
    $telephone = isset($_POST["telephone"]) ? htmlspecialchars(trim($_POST["telephone"])) : null;
    $email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : null;

    if (!$id || !$nom || !$prenom || !$telephone || !$email) {
        echo json_encode([
            "succes" => false,
            "message" => "Tous les champs sont obligatoires pour la modification"
        ]);
        exit;
    }

    $result = Comptable::update($id, $nom, $prenom, $sexe, $adresse, $telephone, $email);
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
    exit;
}

function selectionner_comptables()
{
    $result = Comptable::select_all();
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun comptable trouvé"
        ]);
    }
    exit;
}

function selectionner_un_comptable()
{
    $id = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "ID comptable manquant"
        ]);
        exit;
    }
    $result = Comptable::select_one($id);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result[0]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun comptable trouvé"
        ]);
    }
    exit;
}

function compter_comptables()
{
    $result = Comptable::compterComptables();
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
    exit;
}

function supprimer_comptable()
{
    $id = isset($_POST["id"]) ? htmlspecialchars(trim($_POST["id"])) : null;
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "ID comptable manquant"
        ]);
        exit;
    }
    $result = Comptable::delete($id);
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
    exit;
}

function rechercher_comptables()
{
    $search_term = isset($_GET["search"]) ? htmlspecialchars(trim($_GET["search"])) : null;
    if (!$search_term) {
        echo json_encode([
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ]);
        exit;
    }
    $result = Comptable::search($search_term);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun comptable trouvé"
        ]);
    }
    exit;
}