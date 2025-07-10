import { defineStore } from "pinia";
import axios from "axios";

export const useUtilisateurStore = defineStore("utilisateur", {
  state: () => ({
    utilisateurs: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchUtilisateurs() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/utilisateur/liste/?user=herva&mdp=mdp"
        );
        this.utilisateurs = (response.data.data || []).map(u => ({
          id: u.id,
          nom: u.nom,
          prenom: u.prenom,
          email: u.email,
          telephone: u.telephone,
          adresse: u.adresse,
          sexe: u.sexe,
          role: u.role,
          date_creation: u.date_creation,
          date_modification: u.date_modification,
        }));
        this.loading = false;
      } catch (err) {
        this.error = err;
        this.loading = false;
      }
    },

    async addUtilisateur(utilisateur) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("nom", utilisateur.nom);
        formData.append("prenom", utilisateur.prenom);
        formData.append("email", utilisateur.email);
        formData.append("telephone", utilisateur.telephone);
        formData.append("adresse", utilisateur.adresse);
        formData.append("sexe", utilisateur.sexe);
        formData.append("mot_de_passe", utilisateur.mot_de_passe);
        formData.append("role", utilisateur.role);

        const response = await axios.post(
          "http://localhost/Api_Stock/utilisateur/ajouter/?user=herva&mdp=mdp",
          formData
        );
        if (response.data.succes && response.data.data && response.data.data.length > 0) {
          const u = response.data.data[0];
          this.utilisateurs.push({
            id: u.id,
            nom: u.nom,
            prenom: u.prenom,
            email: u.email,
            telephone: u.telephone,
            adresse: u.adresse,
            sexe: u.sexe,
            role: u.role,
            date_creation: u.date_creation,
            date_modification: u.date_modification,
          });
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err;
        this.loading = false;
        throw err;
      }
    },

    async modifierUtilisateur(utilisateur) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", utilisateur.id);
        formData.append("nom", utilisateur.nom);
        formData.append("prenom", utilisateur.prenom);
        formData.append("email", utilisateur.email);
        formData.append("telephone", utilisateur.telephone);
        formData.append("adresse", utilisateur.adresse);
        formData.append("sexe", utilisateur.sexe);
        formData.append("mot_de_passe", utilisateur.mot_de_passe);
        formData.append("role", utilisateur.role);

        const response = await axios.post(
          "http://localhost/Api_Stock/utilisateur/modifier/?user=herva&mdp=mdp",
          formData
        );
        if (response.data.succes && response.data.data && response.data.data.length > 0) {
          const u = response.data.data[0];
          const idx = this.utilisateurs.findIndex(us => us.id == u.id);
          if (idx !== -1) {
            this.utilisateurs[idx] = {
              id: u.id,
              nom: u.nom,
              prenom: u.prenom,
              email: u.email,
              telephone: u.telephone,
              adresse: u.adresse,
              sexe: u.sexe,
              role: u.role,
              date_creation: u.date_creation,
              date_modification: u.date_modification,
            };
          }
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err;
        this.loading = false;
        throw err;
      }
    },

    // Suppression utilisateur via l'API
    async supprimerUtilisateur(id) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", id);

        const response = await axios.post(
          "http://localhost/Api_Stock/utilisateur/supprimer/?user=herva&mdp=mdp",
          formData
        );
        if (response.data.succes) {
          this.utilisateurs = this.utilisateurs.filter(u => u.id !== id);
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err;
        this.loading = false;
        throw err;
      }
    },
  },
});
