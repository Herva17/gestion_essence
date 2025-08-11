<?php
require_once("./Config.php");

class Fournisseur
{
    public static function save($nom_fournisseur, $adresse, $telephone, $email)
    {
        $data = get_connection();

        // Vérifier si l'email ou le téléphone existe déjà
        $query = $data->prepare("SELECT id_fournisseur FROM fournisseur WHERE email = :email OR telephone = :telephone");
        $query->execute([
            ':email' => $email,
            ':telephone' => $telephone
        ]);
        if ($query->fetch()) {
            return [
                "Message" => "L'email ou le numéro de téléphone existe déjà. Veuillez en choisir un autre."
            ];
        }

        // Insérer le nouveau fournisseur
        $query = $data->prepare("INSERT INTO fournisseur (nom_fournisseur, adresse, telephone, email) 
                                 VALUES (:nom_fournisseur, :adresse, :telephone, :email)");
        $success = $query->execute([
            ':nom_fournisseur' => $nom_fournisseur,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':email' => $email
        ]);

        if ($success) {
            return [
                "Reussite" => "Fournisseur enregistré",
                "Dernier_Enregistrement" => self::get_last()
            ];
        } else {
            return [
                "Message" => "Echec d'enregistrement"
            ];
        }
    }

    public static function delete($id_fournisseur)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM fournisseur WHERE id_fournisseur = :id_fournisseur");
        if ($query->execute([':id_fournisseur' => $id_fournisseur])) {
            return ["Reussite" => "Suppression réussie"];
        } else {
            return ["Message" => "Echec de suppression"];
        }
    }

    public static function update($id_fournisseur, $nom_fournisseur, $adresse, $telephone, $email)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE fournisseur 
            SET nom_fournisseur = :nom_fournisseur, adresse = :adresse, 
                telephone = :telephone, email = :email
            WHERE id_fournisseur = :id_fournisseur");
        $success = $query->execute([
            ':nom_fournisseur' => $nom_fournisseur,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':email' => $email,
            ':id_fournisseur' => $id_fournisseur
        ]);
        if ($success) {
            return ["Reussite" => "Modification réussie"];
        } else {
            return ["Message" => "Echec de modification"];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $stmt = $data->query("SELECT * FROM fournisseur ORDER BY nom_fournisseur");
        $donnees = $stmt->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun fournisseur disponible"];
        }
    }

    public static function select_one($id_fournisseur)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM fournisseur WHERE id_fournisseur = :id_fournisseur");
        $query->execute([':id_fournisseur' => $id_fournisseur]);
        $donnees = $query->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun fournisseur trouvé avec cet ID"];
        }
    }

    public static function get_last()
    {
        $data = get_connection();
        $stmt = $data->query("SELECT * FROM fournisseur ORDER BY id_fournisseur DESC LIMIT 1");
        $donnees = $stmt->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return [];
        }
    }

    public static function count_fournisseurs()
    {
        $data = get_connection();
        $stmt = $data->query("SELECT count(id_fournisseur) as total FROM fournisseur");
        $donnees = $stmt->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucune donnée disponible"];
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM fournisseur 
                                WHERE nom_fournisseur LIKE :search OR email LIKE :search OR telephone LIKE :search");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();

        if ($results && count($results) > 0) {
            return $results;
        } else {
            return ["Message" => "Aucun fournisseur trouvé pour cette recherche"];
        }
    }
}