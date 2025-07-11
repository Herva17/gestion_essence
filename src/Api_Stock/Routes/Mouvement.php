<?php
header('Content-Type: Application/json');
require("./Controlleurs/Mouvement.php");

$data = array();
$url = explode('/', $_SERVER['REQUEST_URI']);
$url_path1 = $url[2] ?? null;
$url_path2 = $url[3] ?? null;

if ($url_path1 == "mouvement") {
    // GET requests
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if ($url_path2 == "lister") {
            lister_mouvements();
            exit;
        } elseif ($url_path2 == "obtenir") {
            obtenir_mouvement();
            exit;
        } elseif ($url_path2 == "compter") {
            compter_mouvements();
            exit;
        } elseif ($url_path2 == "rechercher") {
            rechercher_mouvements();
            exit;
        } elseif ($url_path2 == "filtrer_type") {
            filtrer_par_type();
            exit;
        } elseif ($url_path2 == "filtrer_date") {
            filtrer_par_date();
            exit;
        }
    }
    // POST requests
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($url_path2 == "ajouter") {
            ajouter_mouvement();
            exit;
        } elseif ($url_path2 == "modifier") {
            modifier_mouvement();
            exit;
        } elseif ($url_path2 == "supprimer") {
            supprimer_mouvement();
            exit;
        }
    }
}
