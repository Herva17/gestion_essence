<?php
require_once("./Config.php");

class Utilisateur
{
    public static function enregistrer($nom, $prenom, $email, $telephone, $adresse, $sexe, $mot_de_passe, $role)
    {
        $data = get_connection();

        // Vérifier si l'email ou le téléphone existe déjà
        $query = $data->prepare("SELECT id FROM utilisateurs WHERE email = :email OR telephone = :telephone");
        $query->execute([
            ':email' => $email,
            ':telephone' => $telephone
        ]);
        if ($query->fetch()) {
            return [
                "Message" => "L'email ou le numéro de téléphone existe déjà"
            ];
        }

        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $query = $data->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, adresse, sexe, mot_de_passe, role) 
                                VALUES (:nom, :prenom, :email, :telephone, :adresse, :sexe, :mot_de_passe, :role)");

        $success = $query->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':sexe' => $sexe,
            ':mot_de_passe' => $mot_de_passe_hash,
            ':role' => $role
        ]);

        if ($success) {
            return [
                "Reussite" => "Utilisateur enregistré avec succès",
                "Dernier_Enregistrement" => self::afficher_dernier_enreg()
            ];
        } else {
            return [
                "Message" => "Échec d'enregistrement de l'utilisateur"
            ];
        }
    }

    public static function afficher_dernier_enreg()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM utilisateurs ORDER BY id DESC LIMIT 1")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return [];
        }
    }

    public static function select_all()
    {
        $data = get_connection();
        $donnees = $data->query("SELECT * FROM utilisateurs ORDER BY nom, prenom")->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun utilisateur disponible"];
        }
    }

    public static function update($id, $nom, $prenom, $email, $telephone, $adresse, $sexe, $mot_de_passe, $role)
    {
        $data = get_connection();
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $query = $data->prepare("UPDATE utilisateurs 
                                SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, 
                                    adresse = :adresse, sexe = :sexe, mot_de_passe = :mot_de_passe, role = :role
                                WHERE id = :id");

        $success = $query->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':sexe' => $sexe,
            ':mot_de_passe' => $mot_de_passe_hash,
            ':role' => $role
        ]);

        if ($success) {
            return ["Reussite" => "Utilisateur modifié avec succès"];
        } else {
            return ["Message" => "Échec de modification de l'utilisateur"];
        }
    }

    public static function select_one($id)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $query->execute([':id' => $id]);
        $donnees = $query->fetchAll();
        if ($donnees && count($donnees) > 0) {
            return $donnees;
        } else {
            return ["Message" => "Aucun utilisateur trouvé avec cet ID"];
        }
    }

    public static function delete($id)
    {
        $data = get_connection();
        $query = $data->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $success = $query->execute([':id' => $id]);
        if ($success) {
            return ["Reussite" => "Utilisateur supprimé avec succès"];
        } else {
            return ["Message" => "Échec de suppression de l'utilisateur"];
        }
    }

    public static function search($search_term)
    {
        $data = get_connection();
        $query = $data->prepare("SELECT * FROM utilisateurs 
                                WHERE nom LIKE :search OR prenom LIKE :search OR email LIKE :search OR telephone LIKE :search OR role LIKE :search");
        $query->execute([':search' => "%$search_term%"]);
        $results = $query->fetchAll();

        if ($results && count($results) > 0) {
            return $results;
        } else {
            return ["Message" => "Aucun utilisateur trouvé pour cette recherche"];
        }
    }
public static function connexion($email, $mot_de_passe)
{
    $data = get_connection();
    $query = $data->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $query->execute([':email' => $email]);
    $utilisateur = $query->fetch();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        unset($utilisateur['mot_de_passe']);
        $role = strtolower($utilisateur['role']);
        if ($role === 'admin') {
            $message = "Vous êtes connecté en tant qu'admin";
        } elseif ($role === 'gerant') {
            $message = "Vous êtes connecté en tant que gérant";
        } elseif ($role === 'comptable') {
            $message = "Vous êtes connecté en tant que comptable";
        } else {
            $message = "Vous êtes connecté";
        }
        return [
            "succes" => true,
            "message" => $message,
            "utilisateur" => $utilisateur,
            "role" => $utilisateur['role']
        ];
    } else {
        return [
            "succes" => false,
            "message" => "Email ou mot de passe incorrect"
        ];
    }
}
}