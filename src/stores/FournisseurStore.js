// src/stores/FournisseurStore.js
import { defineStore } from "pinia";
import axios from "axios";

export const useFournisseurStore = defineStore("fournisseur", {
  state: () => ({
    fournisseurs: [],
    loading: false,
    error: null,
    currentFournisseur: null,
  }),
  actions: {
    /**
     * Récupère la liste des fournisseurs depuis l'API
     */
    async fetchFournisseurs() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/fournisseur/select/?user=herva&mdp=mdp"
        );

        // Transformation des données pour éliminer les clés numériques (0,1,2...)
        this.fournisseurs = (response.data.data || []).map(fournisseur => ({
          id: fournisseur.id_fournisseur,
          nom: fournisseur.nom_fournisseur,
          adresse: fournisseur.adresse,
          telephone: fournisseur.telephone,
          email: fournisseur.email
        }));

      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        console.error("Erreur lors de la récupération des fournisseurs:", err);
      } finally {
        this.loading = false;
      }
    },

    /**
     * Ajoute un nouveau fournisseur
     */
    async addFournisseur(fournisseurData) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("nom_fournisseur", fournisseurData.nom);
        formData.append("adresse", fournisseurData.adresse);
        formData.append("telephone", fournisseurData.telephone);
        formData.append("email", fournisseurData.email);

        const response = await axios.post(
          "http://localhost/Api_Stock/fournisseur/enregistrer/?user=herva&mdp=mdp",
          formData
        );

        if (response.data.succes) {
          await this.fetchFournisseurs(); // Rafraîchir la liste
          return response.data;
        }
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Met à jour un fournisseur existant
     */
    async updateFournisseur(fournisseurData) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id_fournisseur", fournisseurData.id);
        formData.append("nom_fournisseur", fournisseurData.nom);
        formData.append("adresse", fournisseurData.adresse);
        formData.append("telephone", fournisseurData.telephone);
        formData.append("email", fournisseurData.email);

        const response = await axios.post(
          "http://localhost/Api_Stock/fournisseur/modifier/?user=herva&mdp=mdp",
          formData
        );

        if (response.data.succes) {
          await this.fetchFournisseurs();
          return response.data;
        }
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Supprime un fournisseur
     */
    async deleteFournisseur(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.delete(
          `http://localhost/Api_Stock/fournisseur/supprimer/${id}/?user=herva&mdp=mdp`
        );

        if (response.data.succes) {
          await this.fetchFournisseurs();
          return response.data;
        }
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    /**
     * Récupère un fournisseur par son ID
     */
    async getFournisseurById(id) {
      try {
        // Si les fournisseurs ne sont pas chargés, on les charge
        if (this.fournisseurs.length === 0) {
          await this.fetchFournisseurs();
        }

        const fournisseur = this.fournisseurs.find(f => f.id === id);
        if (!fournisseur) {
          throw new Error("Fournisseur non trouvé");
        }

        this.currentFournisseur = fournisseur;
        return fournisseur;
      } catch (err) {
        this.error = err.message;
        throw err;
      }
    }
  },
  getters: {
    /**
     * Formatte les fournisseurs pour les options de select
     */
    fournisseurOptions: (state) => {
      return state.fournisseurs.map(f => ({
        label: f.nom,
        value: f.id,
        ...f
      }));
    },

    /**
     * Fournisseurs triés par nom
     */
    sortedFournisseurs: (state) => {
      return [...state.fournisseurs].sort((a, b) =>
        a.nom.localeCompare(b.nom)
      );
    }
  }
});
