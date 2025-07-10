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
        this.commandes = (response.data.me || []).map(cmd => ({
          id: cmd.id,
          id_client: cmd.id_client,
          id_produit: cmd.id_produit,
          id_User: cmd.id_User,
          quantite: cmd.quantite,
          date_commande: cmd.date_commande
        }));
        this.loading = false;
      } catch (err) {
        this.error = err;
        this.loading = false;
      }
    },

    async fetchTotalCommandes() {
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/commande/compter/?user=herva&mdp=mdp"
        );
        this.totalCommandes = response.data.me && response.data.me[0]
          ? response.data.me[0].total
          : 0;
      } catch (err) {
        this.totalCommandes = 0;
      }
    },

    async saveCommande(commande) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id_client", commande.id_client);
        formData.append("id_produit", commande.id_produit);
        formData.append("id_User", commande.id_User);
        formData.append("quantite", commande.quantite);

        const response = await axios.post(
          "http://localhost/Api_Stock/commande/save/?user=herva&mdp=mdp",
          formData
        );

        if (
          response.data.me &&
          response.data.me.Dernier_Enregistrement &&
          response.data.me.Dernier_Enregistrement.length > 0
        ) {
          const cmd = response.data.me.Dernier_Enregistrement[0];
          this.commandes.push({
            id: cmd.id,
            id_client: cmd.id_client,
            id_produit: cmd.id_produit,
            id_User: cmd.id_User,
            quantite: cmd.quantite,
            date_commande: cmd.date_commande,
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

    async updateCommande(commande) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", commande.id);
        formData.append("id_client", commande.id_client);
        formData.append("id_produit", commande.id_produit);
        formData.append("id_User", commande.id_User);
        formData.append("quantite", commande.quantite);

        const response = await axios.post(
          "http://localhost/Api_Stock/commande/update/?user=herva&mdp=mdp",
          formData
        );

        if (response.data.me && response.data.me.Reussite) {
          const index = this.commandes.findIndex(cmd => cmd.id === commande.id);
          if (index !== -1) {
            this.commandes[index] = {
              ...this.commandes[index],
              ...commande
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

    async deleteCommande(id) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", id);

        const response = await axios.post(
          "http://localhost/Api_Stock/commande/delete/?user=herva&mdp=mdp",
          formData
        );

        if (
          response.data.me &&
          response.data.me.Reussite === "Commande supprimée avec succès"
        ) {
          this.commandes = this.commandes.filter(cmd => cmd.id !== id);
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err;
        this.loading = false;
        throw err;
      }
    }
  },
});
