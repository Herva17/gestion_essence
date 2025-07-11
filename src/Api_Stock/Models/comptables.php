<?php
require_once("./Config.php");

class Comptable
{
    public static function enregistrer($nom, $prenom, $sexe, $adresse, $telephone, $email)
    {
        $data = get_connection();

        // Vérifier si l'email ou le téléphone existe déjà
        $query = $data->prepare("SELECT id FROM comptables WHERE email = :email OR telephone = :telephone");
        $query->execute([
            ':email' => $email,
            ':telephone' => $telephone
        ]);
        if ($query->fetch()) {
            return [
                "Message" => "L'email ou le numéro de téléphone existe déjà"
            ];
        }

        $date_creation = date("Y-m-d H:i:s");
        $query = $data->prepare("INSERT INTO comptables (nom, prenom, sexe, adresse, telephone, email, date_creation) 
                                VALUES (:nom, :prenom, :sexe, :adresse, :telephone, :email, :date_creation)");

        $success = $query->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':sexe' => $sexe,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':email' => $email,
            ':date_creation' => $date_creation
        ]);

        if ($success) {
            return [
                "Reussite" => "Comptable enregistré avec succès",
                "Dernier_Enregistrement" => self::afficher_dernier_enreg()
            ];
        } else {
            return [
                "Message" => "Échec d'enregistrement du comptable"
            ];
        }
    }

    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM comptables ORDER BY id DESC LIMIT 1")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return [];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM comptables ORDER BY nom, prenom")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun comptable disponible"];
        }
    }

    public static function update($id, $nom, $prenom, $sexe, $adresse, $telephone, $email)
    {
        $data = get_connection();
        $query = $data->prepare("UPDATE comptables 
                                SET nom = :nom, prenom = :prenom, sexe = :sexe, 
                                    adresse = :adresse, telephone = :telephone, email = :email 
                                WHERE id = :id");

        $success = $query->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':sexe' => $sexe,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':email' => $email
        ]);

        if ($success) {
            return ["Reussite" => "Comptable modifié avec succès"];
        } else {
            return ["Message" => "Échec de modification du comptable"];
        }
    }

    public static function select_one($id)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM comptables WHERE id = :id");
        $query->execute([':id' => $id]);
        $donnees = $query->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun comptable trouvé avec cet ID"];
        }
    }

    public static function compterComptables()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT COUNT(id) as total FROM comptables")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucune donnée disponible"];
        }
    }

    public static function delete($id)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM comptables WHERE id = :id");
        $success = $query->execute([':id' => $id]);
        if ($success) {
            return ["Reussite" => "Comptable supprimé avec succès"];
        } else {
            return ["Message" => "Échec de suppression du comptable"];
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM comptables 
                                WHERE nom LIKE :search OR prenom LIKE :search OR email LIKE :search OR telephone LIKE :search");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();

        if ($results && count($results) > 0) {
            return $results;
        } else {
            return ["Message" => "Aucun comptable trouvé pour cette recherche"];
        }
    }
}