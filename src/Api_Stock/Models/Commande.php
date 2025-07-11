<?php
require_once("./Config.php");

class Commande
{
    public $response = array();
    public static function enregistrer($id_client, $id_produit, $id_comptable, $quantite)
    {
        $data = get_connection();
        $date_commande = date("Y-m-d H:i:s");

        // Récupérer les infos du produit
        $query = $data->prepare("SELECT nom, quantite, prix_unitaire FROM produits WHERE id = :id_produit");
        $query->execute([':id_produit' => $id_produit]);
        $produit = $query->fetch();

        if (!$produit) {
            return ["Message" => "Produit introuvable"];
        }

        if ($quantite > $produit['quantite']) {
            return ["Message" => "Quantité commandée supérieure au stock disponible"];
        }

        // Enregistrer la commande
        $query = $data->prepare("INSERT INTO commandes (id_client, id_produit, id_User, quantite, date_commande) 
                            VALUES (:id_client, :id_produit, :id_User, :quantite, :date_commande)");
        $success = $query->execute([
            ':id_client' => $id_client,
            ':id_produit' => $id_produit,
            ':id_User' => $id_comptable,
            ':quantite' => $quantite,
            ':date_commande' => $date_commande
        ]);

        if ($success) {
            // Mettre à jour la quantité du produit
            $query = $data->prepare("UPDATE produits SET quantite = quantite - :quantite WHERE id = :id_produit");
            $query->execute([
                ':quantite' => $quantite,
                ':id_produit' => $id_produit
            ]);

            // Enregistrer le mouvement (sortie)
            $designation = $produit['nom'];
            $prix_unitaire = $produit['prix_unitaire'];
            $type = "sortie";
            $date_mouvement = $date_commande;
            $query_mvt = $data->prepare("INSERT INTO mouvements (designation, Quantite, Prix_Unitaire, type, date_mouvement)
                                     VALUES (:designation, :quantite, :prix_unitaire, :type, :date_mouvement)");
            $query_mvt->execute([
                ':designation' => $designation,
                ':quantite' => $quantite,
                ':prix_unitaire' => $prix_unitaire,
                ':type' => $type,
                ':date_mouvement' => $date_mouvement
            ]);

            return [
                "Reussite" => "Commande enregistrée avec succès",
                "Dernier_Enregistrement" => self::afficher_dernier_enreg()
            ];
        } else {
            return ["Message" => "Échec d'enregistrement de la commande"];
        }
    }
    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM commandes ORDER BY id DESC LIMIT 1")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM commandes ORDER BY date_commande DESC")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune commande disponible";
            return $response;
        }
    }

    public static function update($id, $id_client, $id_produit, $id_comptable, $quantite)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE commandes 
                                SET id_client = :id_client, 
                                    id_produit = :id_produit,
                                    id_comptable = :id_comptable,
                                    quantite = :quantite
                                WHERE id = :id");

        if ($query->execute([
            ':id' => $id,
            ':id_client' => $id_client,
            ':id_produit' => $id_produit,
            ':id_comptable' => $id_comptable,
            ':quantite' => $quantite
        ])) {
            $response["message"] = "Commande modifiée avec succès";
            return $response;
        } else {
            $response["Message"] = "Échec de modification de la commande";
            return $response;
        }
    }

    public static function select_one($id)
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM commandes WHERE id = '$id'")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune commande trouvée avec cet ID";
            return $response;
        }
    }

    public static function compterCommandes()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id) as total FROM commandes")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune donnée disponible";
            return $response;
        }
    }

    public static function delete($id)
    {
        $data = get_connection();
        if ($data->query("DELETE FROM commandes WHERE id = '$id'")) {
            $response["Reussite"] = "Commande supprimée avec succès";
            return $response;
        } else {
            $response["Message"] = "Échec de suppression de la commande";
            return $response;
        }
    }

    public static function commandes_par_client($id_client)
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM commandes WHERE id_client = '$id_client' ORDER BY date_commande DESC")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune commande trouvée pour ce client";
            return $response;
        }
    }

    public static function commandes_par_produit($id_produit)
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM commandes WHERE id_produit = '$id_produit' ORDER BY date_commande DESC")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["message"] = "Aucune commande trouvée pour ce produit";
            return $response;
        }
    }
}
