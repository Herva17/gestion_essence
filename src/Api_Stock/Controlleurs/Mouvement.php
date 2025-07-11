<?php
require_once("./Models/Mouvement.php");

function ajouter_mouvement() {
    // Récupération des données POST
    $designation = isset($_POST['designation']) ? trim($_POST['designation']) : null;
    $type = isset($_POST['type']) ? trim($_POST['type']) : null;
    $date_mouvement = isset($_POST['date_mouvement']) ? trim($_POST['date_mouvement']) : date('Y-m-d H:i:s');

    // Validation des données
    if (!$designation || !$type) {
        echo json_encode([
            'succes' => false,
            'message' => 'Designation et type sont obligatoires'
        ]);
        return;
    }

    // Appel du modèle
    $result = Mouvement::save($designation, $type, $date_mouvement);
    echo json_encode($result);
}

function modifier_mouvement() {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $designation = isset($_POST['designation']) ? trim($_POST['designation']) : null;
    $type = isset($_POST['type']) ? trim($_POST['type']) : null;
    $date_mouvement = isset($_POST['date_mouvement']) ? trim($_POST['date_mouvement']) : null;

    if (!$id || !$designation || !$type || !$date_mouvement) {
        echo json_encode([
            'succes' => false,
            'message' => 'Tous les champs sont obligatoires'
        ]);
        return;
    }

    $result = Mouvement::update($id, $designation, $type, $date_mouvement);
    echo json_encode($result);
}

function supprimer_mouvement() {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if (!$id) {
        echo json_encode([
            'succes' => false,
            'message' => 'ID mouvement manquant'
        ]);
        return;
    }

    $result = Mouvement::delete($id);
    echo json_encode($result);
}

function lister_mouvements() {
    $result = Mouvement::select_all();
    echo json_encode($result);
}

function obtenir_mouvement() {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        echo json_encode([
            'succes' => false,
            'message' => 'ID mouvement manquant'
        ]);
        return;
    }

    $result = Mouvement::select_one($id);
    echo json_encode($result);
}

function compter_mouvements() {
    $result = Mouvement::count();
    echo json_encode($result);
}

function rechercher_mouvements() {
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;

    if (!$search) {
        echo json_encode([
            'succes' => false,
            'message' => 'Terme de recherche manquant'
        ]);
        return;
    }

    $result = Mouvement::search($search);
    echo json_encode($result);
}

function filtrer_par_type() {
    $type = isset($_GET['type']) ? trim($_GET['type']) : null;

    if (!$type) {
        echo json_encode([
            'succes' => false,
            'message' => 'Type manquant'
        ]);
        return;
    }

    $result = Mouvement::filter_by_type($type);
    echo json_encode($result);
}

function filtrer_par_date() {
    $date_debut = isset($_GET['date_debut']) ? trim($_GET['date_debut']) : null;
    $date_fin = isset($_GET['date_fin']) ? trim($_GET['date_fin']) : null;

    if (!$date_debut || !$date_fin) {
        echo json_encode([
            'succes' => false,
            'message' => 'Dates début et fin sont obligatoires'
        ]);
        return;
    }

    $result = Mouvement::filter_by_date($date_debut, $date_fin);
    echo json_encode($result);
}