import { defineStore } from "pinia";
import axios from "axios";

export const useProduitStore = defineStore("produit", {
  state: () => ({
    produits: [],
    totalProduits: 0,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchProduits() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/produit/select_all/?user=herva&mdp=mdp"
        );
        this.produits = (response.data.response.data || []).map(prod => ({
          id: prod.id,
          nom: prod.nom,
          description: prod.description,
          quantite: prod.quantite,
          prix_unitaire: prod.prix_unitaire,
          id_categorie: prod.id_categorie,
          id_User: prod.id_User,
          date_creation: prod.date_creation
        }));
        this.loading = false;
      } catch (err) {
        this.error = err;
        this.loading = false;
      }
    },
    async fetchTotalProduits() {
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/produit/compter/?user=herva&mdp=mdp"
        );
        this.totalProduits = response.data.response.total || 0;
      } catch (err) {
        this.totalProduits = 0;
      }
    },
    // Ajout d'un produit via l'API (adapté pour FormData)
    async saveProduit(produit) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        // N'envoie pas la date_creation, elle sera générée côté API
        Object.entries(produit).forEach(([key, value]) => {
          if (key !== 'date_creation') {
            formData.append(key, value);
          }
        });

        const response = await axios.post(
          "http://localhost/Api_Stock/produit/enregistrer/?user=herva&mdp=mdp",
          formData
        );

        if (
          response.data.response &&
          response.data.response.succes &&
          response.data.response.data &&
          response.data.response.data.length > 0
        ) {
          const prod = response.data.response.data[0];
          this.produits.push({
            id: prod.id,
            nom: prod.nom,
            description: prod.description,
            quantite: prod.quantite,
            prix_unitaire: prod.prix_unitaire,
            id_categorie: prod.id_categorie,
            id_User: prod.id_User,
            date_creation: prod.date_creation
          });
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
