<?php
require_once("./Config.php");

class Approvisionnement
{
    public static function save($id_user, $id_produit, $id_fournisseur, $quantite, $prix_unitaire)
    {
        $data = get_connection();
        
        // Génération automatique de la date courante
        $date_approvisionnement = date('Y-m-d H:i:s');

        $query = $data->prepare("INSERT INTO approvisionnement 
                                (id_User, id_produit, id_fournisseur, date_approvisionnement, quantite, prix_unitaire) 
                                VALUES (:id_user, :id_produit, :id_fournisseur, :date_approvisionnement, :quantite, :prix_unitaire)");
        $success = $query->execute([
            ':id_user' => $id_user,
            ':id_produit' => $id_produit,
            ':id_fournisseur' => $id_fournisseur,
            ':date_approvisionnement' => $date_approvisionnement,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prix_unitaire
        ]);

        if ($success) {
            return [
                "Reussite" => "Approvisionnement enregistré",
                "Dernier_Enregistrement" => self::get_last()
            ];
        } else {
            return [
                "Message" => "Echec d'enregistrement"
            ];
        }
    }

    public static function delete($id_approvisionnement)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM approvisionnement WHERE id_approvisionnement = :id");
        if ($query->execute([':id' => $id_approvisionnement])) {
            return ["Reussite" => "Suppression réussie"];
        } else {
            return ["Message" => "Echec de suppression"];
        }
    }

    public static function update($id_approvisionnement, $id_user, $id_produit, $id_fournisseur, $quantite, $prix_unitaire)
    {
        $data = get_connection();
        
        // Génération automatique de la date de mise à jour
        $date_mise_a_jour = date('Y-m-d H:i:s');

        $query = $data->prepare("UPDATE approvisionnement 
                                SET id_User = :id_user,
                                    id_produit = :id_produit, 
                                    id_fournisseur = :id_fournisseur, 
                                    date_approvisionnement = :date_mise_a_jour, 
                                    quantite = :quantite, 
                                    prix_unitaire = :prix_unitaire
                                WHERE id_approvisionnement = :id");
        $success = $query->execute([
            ':id_user' => $id_user,
            ':id_produit' => $id_produit,
            ':id_fournisseur' => $id_fournisseur,
            ':date_mise_a_jour' => $date_mise_a_jour,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prix_unitaire,
            ':id' => $id_approvisionnement
        ]);
        
        if ($success) {
            return [
                "status" => "success",
                "message" => "Approvisionnement mis à jour avec succès",
                "date_mise_a_jour" => $date_mise_a_jour
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Échec de la mise à jour de l'approvisionnement"
            ];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $query = $data->query("SELECT a.*, p.designation as produit_nom, f.nom_fournisseur, u.nom as user_nom 
                             FROM approvisionnement a
                             JOIN produit p ON a.id_produit = p.id
                             JOIN fournisseur f ON a.id_fournisseur = f.id_fournisseur
                             JOIN utilisateurs u ON a.id_User = u.id
                             ORDER BY a.date_approvisionnement DESC");
        $donnees = $query->fetchAll();
        
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun approvisionnement disponible"];
        }
    }

    public static function select_one($id_approvisionnement)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT a.*, p.nom as produit_nom, f.nom_fournisseur, u.nom as user_nom 
                                FROM approvisionnement a
                                JOIN produits p ON a.id_produit = p.id
                                JOIN fournisseurs f ON a.id_fournisseur = f.id_fournisseur
                                JOIN users u ON a.id_User = u.id
                                WHERE a.id_approvisionnement = :id");
        $query->execute([':id' => $id_approvisionnement]);
        $donnees = $query->fetch();
        
        if ($donnees) {
            return $donnees;
        } else {
            return ["Message" => "Aucun approvisionnement trouvé avec cet ID"];
        }
    }

    public static function get_last()
    {
        $data = get_connection();
        $query = $data->query("SELECT * FROM approvisionnement ORDER BY id_approvisionnement DESC LIMIT 1");
        $donnees = $query->fetch();
        
        if ($donnees) {
            return $donnees;
        } else {
            return [];
        }
    }

    public static function count()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id_approvisionnement) as total FROM approvisionnement")->fetchAll();
        if (count($donnees) > 0) {
            return $donnees;
        } else {
            $response["Message"] = "Aucune donnée disponible";
            return $response;
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT a.*, p.nom as produit_nom, f.nom_fournisseur, u.nom as user_nom 
                               FROM approvisionnement a
                               JOIN produits p ON a.id_produit = p.id
                               JOIN fournisseurs f ON a.id_fournisseur = f.id_fournisseur
                               JOIN users u ON a.id_User = u.id
                               WHERE p.nom LIKE :search OR f.nom_fournisseur LIKE :search OR u.nom LIKE :search
                               ORDER BY a.date_approvisionnement DESC");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();

        if ($results && count($results) > 0) {
            return $results;
        } else {
            return ["Message" => "Aucun approvisionnement trouvé pour cette recherche"];
        }
    }
}