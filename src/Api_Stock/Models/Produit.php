<?php
require_once("./Config.php");

class Produit
{
public static function enregistrer($nom, $description, $quantite, $prix_unitaire, $id_categorie, $id_gerant)
{
    $data = get_connection();
    $date_creation = date("Y-m-d H:i:s");

    // 1. Enregistrement du produit
    $query = $data->prepare("INSERT INTO produits (nom, description, quantite, prix_unitaire, id_categorie, id_User, date_creation) 
                            VALUES (:nom, :description, :quantite, :prix_unitaire, :id_categorie, :id_User, :date_creation)");

    $success = $query->execute([
        ':nom' => $nom,
        ':description' => $description,
        ':quantite' => $quantite,
        ':prix_unitaire' => $prix_unitaire,
        ':id_categorie' => $id_categorie,
        ':id_User' => $id_gerant,
        ':date_creation' => $date_creation
    ]);

    if ($success) {
        // 2. Récupérer le dernier produit inséré
        $produit = self::afficher_dernier_enreg();
        $designation = $nom; // ou $produit[0]['nom'] si tu veux être sûr

        // 3. Enregistrement du mouvement (sans id_approvisionnement)
        $type = "entrée";
        $date_mouvement = $date_creation;
        $query_mvt = $data->prepare("INSERT INTO mouvements (designation,Quantite,Prix_Unitaire, type, date_mouvement) 
                                     VALUES (:designation, :Quantite, :Prix_Unitaire, :type, :date_mouvement)");
        $query_mvt->execute([
            ':designation' => $designation,
            ':Quantite' => $quantite,
            ':Prix_Unitaire' => $prix_unitaire,
            ':type' => $type,
            ':date_mouvement' => $date_mouvement
        ]);

        return [
            "Reussite" => "Produit enregistré avec succès",
            "Dernier_Enregistrement" => $produit
        ];
    } else {
        return [
            "Message" => "Échec d'enregistrement du produit"
        ];
    }
}

    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM produits ORDER BY id DESC LIMIT 1")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return [];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM produits ORDER BY nom")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun produit disponible"];
        }
    }

    public static function update($id, $nom, $description, $quantite, $prix_unitaire, $id_categorie, $id_approvisionnement, $id_gerant)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE produits 
                                SET nom = :nom, 
                                    description = :description,
                                    quantite = :quantite,
                                    prix_unitaire = :prix_unitaire,
                                    id_categorie = :id_categorie,
                                    id_approvisionnement = :id_approvisionnement,
                                    id_gerant = :id_gerant
                                WHERE id = :id");

        $success = $query->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prix_unitaire,
            ':id_categorie' => $id_categorie,
            ':id_approvisionnement' => $id_approvisionnement,
            ':id_gerant' => $id_gerant
        ]);

        if ($success) {
            return ["Reussite" => "Produit modifié avec succès"];
        } else {
            return ["Message" => "Échec de modification du produit"];
        }
    }

    public static function select_one($id)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM produits WHERE id = :id");
        $query->execute([':id' => $id]);
        $donnees = $query->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun produit trouvé avec cet ID"];
        }
    }

    public static function compterProduits()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id) as total FROM produits")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucune donnée disponible"];
        }
    }

    public static function delete($id)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM produits WHERE id = :id");
        $success = $query->execute([':id' => $id]);
        if ($success) {
            return ["Reussite" => "Produit supprimé avec succès"];
        } else {
            return ["Message" => "Échec de suppression du produit"];
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM produits 
                                WHERE nom LIKE :search OR description LIKE :search");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();

        if ($results && count($results) > 0) {
            return $results;
        } else {
            return ["Message" => "Aucun produit trouvé pour cette recherche"];
        }
    }

    public static function produits_par_categorie($id_categorie)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM produits WHERE id_categorie = :id_categorie");
        $query->execute([':id_categorie' => $id_categorie]);
        $donnees = $query->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun produit trouvé dans cette catégorie"];
        }
    }
}