import { defineStore } from "pinia";
import axios from "axios";

export const useCommandeStore = defineStore("commande", {
  state: () => ({
    commandes: [],
    totalCommandes: 0,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchCommandes() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/commande/select/?user=herva&mdp=mdp"
        );
        
        this.commandes = (response.data.data || []).map(cmd => ({
          id: cmd.id,
          id_client: cmd.id_client,
          Id_appro: cmd.Id_appro,
          id_User: cmd.id_User,
          quantite: cmd.quantite,
          date_commande: cmd.date_commande,
          produit: cmd.produit,
          prix_unitaire: cmd.prix_unitaire,
          client: cmd.client,
          comptable: cmd.comptable
        }));
        this.loading = false;
      } catch (err) {
        this.error = err.message || "Erreur lors du chargement des commandes";
        this.loading = false;
      }
    },

    async fetchTotalCommandes() {
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/commande/compter/?user=herva&mdp=mdp"
        );
        this.totalCommandes = response.data.data && response.data.data[0]
          ? response.data.data[0].total
          : 0;
      } catch (err) {
        this.totalCommandes = 0;
        console.error("Erreur comptage commandes:", err);
      }
    },

    async saveCommande(commande) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id_client", commande.id_client);
        formData.append("id_appro", commande.Id_appro);
        formData.append("id_User", commande.id_User);
        formData.append("quantite", commande.quantite);

        const response = await axios.post(
          "http://localhost/Api_Stock/commande/save/?user=herva&mdp=mdp",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        console.log("Réponse API Enregistrement :", response.data);

        if (response.data.succes && response.data.data) {
          const newCommande = {
            id: response.data.data[0].id,
            id_client: response.data.data[0].id_client,
            Id_appro: response.data.data[0].Id_appro,
            id_User: response.data.data[0].id_User,
            quantite: response.data.data[0].quantite,
            date_commande: response.data.data[0].date_commande,
            produit: response.data.data[0].produit,
            prix_unitaire: response.data.data[0].prix_unitaire,
            client: response.data.data[0].client,
            comptable: response.data.data[0].comptable
          };
          this.commandes.unshift(newCommande);
          this.totalCommandes += 1;
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        this.loading = false;
        throw err;
      }
    },

    async updateCommande(commande) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", commande.id);
        formData.append("id_client", commande.id_client);
        formData.append("id_appro", commande.Id_appro);
        formData.append("id_User", commande.id_User);
        formData.append("quantite", commande.quantite);

        const response = await axios.post(
          "http://localhost/Api_Stock/commande/update/?user=herva&mdp=mdp",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        if (response.data.succes) {
          const index = this.commandes.findIndex(cmd => cmd.id === commande.id);
          if (index !== -1) {
            this.commandes[index] = {
              ...this.commandes[index],
              ...commande,
              produit: commande.produit || this.commandes[index].produit,
              prix_unitaire: commande.prix_unitaire || this.commandes[index].prix_unitaire
            };
          }
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        this.loading = false;
        throw err;
      }
    },

    async deleteCommande(id) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", id);

        const response = await axios.post(
          "http://localhost/Api_Stock/commande/delete/?user=herva&mdp=mdp",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        if (response.data.succes) {
          this.commandes = this.commandes.filter(cmd => cmd.id !== id);
          this.totalCommandes -= 1;
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        this.loading = false;
        throw err;
      }
    },

    async fetchCommandesByClient(id_client) {
      this.loading = true;
      try {
        const response = await axios.get(
          `http://localhost/Api_Stock/commande/commandes_par_client/?id_client=${id_client}&user=herva&mdp=mdp`
        );
        return response.data.data || [];
      } catch (err) {
        console.error("Erreur commandes par client:", err);
        return [];
      } finally {
        this.loading = false;
      }
    },

    async fetchCommandesByProduit(id_produit) {
      this.loading = true;
      try {
        const response = await axios.get(
          `http://localhost/Api_Stock/commande/commandes_par_produit/?id_produit=${id_produit}&user=herva&mdp=mdp`
        );
        return response.data.data || [];
      } catch (err) {
        console.error("Erreur commandes par produit:", err);
        return [];
      } finally {
        this.loading = false;
      }
    }
  },
  getters: {
    getCommandes: (state) => state.commandes,
    getTotalCommandes: (state) => state.totalCommandes,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
  }
});