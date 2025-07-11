<?php
require_once("./Models/Utilisateurs.php");

function ajouter_utilisateur()
{
    header('Content-Type: application/json');
    $nom = isset($_POST["nom"]) ? trim($_POST["nom"]) : null;
    $prenom = isset($_POST["prenom"]) ? trim($_POST["prenom"]) : null;
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
    $telephone = isset($_POST["telephone"]) ? trim($_POST["telephone"]) : null;
    $adresse = isset($_POST["adresse"]) ? trim($_POST["adresse"]) : null;
    $sexe = isset($_POST["sexe"]) ? trim($_POST["sexe"]) : null;
    $mot_de_passe = isset($_POST["mot_de_passe"]) ? $_POST["mot_de_passe"] : null;
    $role = isset($_POST["role"]) ? trim($_POST["role"]) : null;

    if (!$nom || !$prenom || !$email || !$telephone || !$mot_de_passe || !$role) {
        echo json_encode([
            "succes" => false,
            "message" => "Paramètres obligatoires manquants (nom, prénom, email, téléphone, mot de passe, rôle)"
        ]);
        exit;
    }

    $result = Utilisateur::enregistrer($nom, $prenom, $email, $telephone, $adresse, $sexe, $mot_de_passe, $role);

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

function supprimer_utilisateur()
{
    header('Content-Type: application/json');
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "Paramètre id manquant"
        ]);
        exit;
    }
    $result = Utilisateur::delete($id);
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

function modifier_utilisateur()
{
    header('Content-Type: application/json');
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    $nom = isset($_POST["nom"]) ? trim($_POST["nom"]) : null;
    $prenom = isset($_POST["prenom"]) ? trim($_POST["prenom"]) : null;
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
    $telephone = isset($_POST["telephone"]) ? trim($_POST["telephone"]) : null;
    $adresse = isset($_POST["adresse"]) ? trim($_POST["adresse"]) : null;
    $sexe = isset($_POST["sexe"]) ? trim($_POST["sexe"]) : null;
    $mot_de_passe = isset($_POST["mot_de_passe"]) ? $_POST["mot_de_passe"] : null;
    $role = isset($_POST["role"]) ? trim($_POST["role"]) : null;

    if (!$id || !$nom || !$prenom || !$email || !$telephone || !$mot_de_passe || !$role) {
        echo json_encode([
            "succes" => false,
            "message" => "Paramètres obligatoires manquants pour la modification"
        ]);
        exit;
    }

    $result = Utilisateur::update($id, $nom, $prenom, $email, $telephone, $adresse, $sexe, $mot_de_passe, $role);
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

function selectionner_utilisateurs()
{
    header('Content-Type: application/json');
    $result = Utilisateur::select_all();
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun utilisateur trouvé"
        ]);
    }
    exit;
}

function selectionner_utilisateur()
{
    header('Content-Type: application/json');
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : null;
    if (!$id) {
        echo json_encode([
            "succes" => false,
            "message" => "Paramètre id manquant"
        ]);
        exit;
    }
    $result = Utilisateur::select_one($id);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result[0]
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun utilisateur trouvé"
        ]);
    }
    exit;
}

function rechercher_utilisateurs()
{
    header('Content-Type: application/json');
    $search_term = isset($_GET["search"]) ? trim($_GET["search"]) : null;
    if (!$search_term) {
        echo json_encode([
            "succes" => false,
            "message" => "Terme de recherche manquant"
        ]);
        exit;
    }
    $result = Utilisateur::search($search_term);
    if (isset($result[0]) && is_array($result[0])) {
        echo json_encode([
            "succes" => true,
            "data" => $result
        ]);
    } else {
        echo json_encode([
            "succes" => false,
            "message" => $result["Message"] ?? "Aucun utilisateur trouvé"
        ]);
    }
    exit;
}

function connexion_utilisateur()
{
    header('Content-Type: application/json');
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;
    $mot_de_passe = isset($_POST["mot_de_passe"]) ? $_POST["mot_de_passe"] : null;

    if (!$email || !$mot_de_passe) {
        echo json_encode([
            "succes" => false,
            "message" => "Email et mot de passe sont obligatoires"
        ]);
        exit;
    }

    $result = Utilisateur::connexion($email, $mot_de_passe);
    echo json_encode($result);
    exit;
}