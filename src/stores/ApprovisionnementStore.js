import { defineStore } from "pinia";
import axios from "axios";

export const useApprovisionnementStore = defineStore("approvisionnement", {
  state: () => ({
    approvisionnements: [],
    totalApprovisionnements: 0,
    loading: false,
    error: null,
    lastFetch: null
  }),

  actions: {
    async fetchApprovisionnements(force = false) {
      if (!force && this.loading) return;
      
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/approvisionnement/select/?user=herva&mdp=mdp"
        );
        
        if (response.data?.success === false) {
          throw new Error(response.data.message || "Erreur serveur");
        }

        this.approvisionnements = response.data?.data || [];
        this.lastFetch = new Date();
        return this.approvisionnements;
      } catch (err) {
        this.error = err.message || "Erreur lors du chargement";
        console.error("fetchApprovisionnements error:", err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async countApprovisionnements() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/approvisionnement/compter/?user=herva&mdp=mdp"
        );

        console.log("API Response count:", response.data);

        if (!response.data?.succes) {
          throw new Error(response.data?.message || "Format de réponse invalide");
        }

        // Plusieurs formats de réponse possibles
        const total = response.data.data?.total?.[0]?.total || 
                     response.data.data?.[0]?.total || 
                     response.data.total || 
                     0;

        this.totalApprovisionnements = Number(total) || 0;
        return this.totalApprovisionnements;

      } catch (err) {
        this.error = err.message || "Erreur lors du comptage";
        console.error("countApprovisionnements error:", err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async saveApprovisionnement(approData) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append('id_User', approData.id_User || 1);
        formData.append('id_produit', approData.id_produit);
        formData.append('id_fournisseur', approData.id_fournisseur);
        formData.append('quantite', approData.quantite);
        formData.append('prix_unitaire', approData.prix_unitaire);

        const response = await axios.post(
          "http://localhost/Api_Stock/approvisionnement/save/?user=herva&mdp=mdp",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        if (!response.data?.success) {
          throw new Error(response.data?.message || "Erreur lors de l'enregistrement");
        }

        await Promise.all([
          this.fetchApprovisionnements(true),
          this.countApprovisionnements()
        ]);

        return response.data;
      } catch (err) {
        this.error = err.message || "Erreur lors de l'enregistrement";
        console.error("saveApprovisionnement error:", err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async deleteApprovisionnement(id) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append('id_approvisionnement', id);

        const response = await axios.post(
          `http://localhost/Api_Stock/approvisionnement/delete/?user=herva&mdp=mdp`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        if (!response.data?.success) {
          throw new Error(response.data?.message || "Erreur lors de la suppression");
        }

        await Promise.all([
          this.fetchApprovisionnements(true),
          this.countApprovisionnements()
        ]);

        return response.data;
      } catch (err) {
        this.error = err.message || "Erreur lors de la suppression";
        console.error("deleteApprovisionnement error:", err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateApprovisionnement(approData) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append('id_approvisionnement', approData.id);
        formData.append('id_produit', approData.id_produit);
        formData.append('id_fournisseur', approData.id_fournisseur);
        formData.append('quantite', approData.quantite);
        formData.append('prix_unitaire', approData.prix_unitaire);

        const response = await axios.post(
          "http://localhost/Api_Stock/approvisionnement/update/?user=herva&mdp=mdp",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        if (!response.data?.success) {
          throw new Error(response.data?.message || "Erreur lors de la modification");
        }

        await Promise.all([
          this.fetchApprovisionnements(true),
          this.countApprovisionnements()
        ]);

        return response.data;
      } catch (err) {
        this.error = err.message || "Erreur lors de la modification";
        console.error("updateApprovisionnement error:", err);
        throw err;
      } finally {
        this.loading = false;
      }
    }
  },

  getters: {
    getById: (state) => (id) => state.approvisionnements.find(a => a.id_approvisionnement === id),
    getByProduit: (state) => (id_produit) => state.approvisionnements.filter(a => a.id_produit === id_produit),
    getByFournisseur: (state) => (id_fournisseur) => state.approvisionnements.filter(a => a.id_fournisseur === id_fournisseur),
    getTotalApprovisionnements: (state) => state.totalApprovisionnements,
    isLoading: (state) => state.loading,
    getError: (state) => state.error
  }
});