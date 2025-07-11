<?php
header('Content-Type: Application/json');
require_once("./Controlleurs/comptables.php");
$data = array();
$url = explode('/', $_SERVER['REQUEST_URI']);
//print_r($url);
$url_path1 = $url[2];
$url_path2 = $url[3];
// Dans votre fichier de routes
if ($url_path1 == "comptable") {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if ($url_path2 == "select") {
            $data["me"] = selectionner_comptables();
            exit();
        } elseif ($url_path2 == "select_one") {
            $data["me"] = selectionner_un_comptable();
              exit();
        } elseif ($url_path2 == "compter") {
            $data["me"] = compter_comptables();
              exit();
        } elseif ($url_path2 == "search") {
            $data["me"] = rechercher_comptables();
              exit();
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($url_path2 == "save") {
            $data["me"] = enregistrer_comptable();
              exit();
        } elseif ($url_path2 == "update") {
            $data["me"] = modification_comptable();
              exit();
        } elseif ($url_path2 == "delete") {
            $data["me"] = supprimer_comptable();
              exit();
        }
    }
}