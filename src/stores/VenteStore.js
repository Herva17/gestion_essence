import { defineStore } from "pinia";
import axios from "axios";

export const useVenteStore = defineStore("vente", {
  state: () => ({
    ventes: [],
    totalVentes: 0,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchVentes() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/vente/select/?user=herva&mdp=mdp"
        );

        this.ventes = (response.data.data || []).map(vente => ({
          id_vente: vente.id_vente,
          id_commande: vente.id_commande,
          quantite: vente.quantite,
          date_vente: vente.date_vente,
          id_client: vente.id_client,
          client: vente.client,
          produit: vente.produit,
          prix_unitaire: vente.prix_unitaire,
          montant_total: vente.montant_total,
          vendeur: vente.vendeur
        }));
        this.loading = false;
      } catch (err) {
        this.error = err.message || "Erreur lors du chargement des ventes";
        this.loading = false;
      }
    },

    async fetchTotalVentes() {
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/vente/compter/?user=herva&mdp=mdp"
        );
        this.totalVentes = response.data.data && response.data.data[0]
          ? response.data.data[0].total
          : 0;
      } catch (err) {
        this.totalVentes = 0;
        console.error("Erreur comptage ventes:", err);
      }
    },

    async saveVente(vente) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id_commande", vente.id_commande);
        formData.append("quantite", vente.quantite);
        formData.append("date_vente", vente.date_vente || new Date().toISOString().split('T')[0]);

        const response = await axios.post(
          "http://localhost/Api_Stock/vente/save/?user=herva&mdp=mdp",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        console.log("Réponse API Enregistrement :", response.data);

        if (response.data.succes && response.data.data) {
          const newVente = {
            id_vente: response.data.data[0].id_vente,
            id_commande: response.data.data[0].id_commande,
            quantite: response.data.data[0].quantite,
            date_vente: response.data.data[0].date_vente,
            id_client: response.data.data[0].id_client,
            client: response.data.data[0].client,
            produit: response.data.data[0].produit,
            prix_unitaire: response.data.data[0].prix_unitaire,
            montant_total: response.data.data[0].montant_total,
            vendeur: response.data.data[0].vendeur
          };
          this.ventes.unshift(newVente);
          this.totalVentes += 1;
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        this.loading = false;
        throw err;
      }
    },

    async fetchVenteById(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          `http://localhost/Api_Stock/vente/select_one/?id_vente=${id}&user=herva&mdp=mdp`
        );

        if (response.data.succes && response.data.data) {
          const vente = response.data.data[0];
          return {
            id_vente: vente.id_vente,
            id_commande: vente.id_commande,
            quantite: vente.quantite,
            date_vente: vente.date_vente,
            id_client: vente.id_client,
            client: vente.client,
            produit: vente.produit,
            prix_unitaire: vente.prix_unitaire,
            montant_total: vente.montant_total,
            vendeur: vente.vendeur
          };
        }
        return null;
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchFicheJournaliere() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/vente/fiche_journaliere/?user=herva&mdp=mdp"
        );

        if (response.data.succes) {
          return response.data.data.map(fiche => ({
            date_vente: fiche.date_vente,
            produit: fiche.produit,
            quantite: fiche.quantite,
            prix_usd: fiche.prix_usd,
            total_usd: fiche.total_usd,
            total_fc: fiche.total_fc,
            client: fiche.client,
            vendeur: fiche.vendeur
          }));
        }
        return [];
      } catch (err) {
        this.error = err.response?.data?.message || err.message;
        return [];
      } finally {
        this.loading = false;
      }
    }
  },
  getters: {
    getVentes: (state) => state.ventes,
    getTotalVentes: (state) => state.totalVentes,
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
  }
});
