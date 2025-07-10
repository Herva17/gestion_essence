import { defineStore } from "pinia";
import axios from "axios";

export const useCategorieStore = defineStore("categorie", {
  state: () => ({
    categories: [],
    totalCategories: 0,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchCategories() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/categorie/select/?user=herva&mdp=mdp"
        );
        this.categories = (response.data.me || []).map(cat => ({
          id: cat.id,
          designation: cat.designation,
          description: cat.description,
          date_creation: cat.date_creation
        }));
        this.loading = false;
      } catch (err) {
        this.error = err;
        this.loading = false;
      }
    },
    async fetchTotalCategories() {
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/categorie/compter/?user=herva&mdp=mdp"
        );
        this.totalCategories = response.data.me && response.data.me[0]
          ? response.data.me[0].total
          : 0;
      } catch (err) {
        this.totalCategories = 0;
      }
    },
    // Ajout d'une catégorie via l'API
    async saveCategorie(categorie) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("designation", categorie.designation);
        formData.append("description", categorie.description);

        const response = await axios.post(
          "http://localhost/Api_Stock/categorie/save/?user=herva&mdp=mdp",
          formData
        );

        if (
          response.data.me &&
          response.data.me.Dernier_Enregistrement &&
          response.data.me.Dernier_Enregistrement.length > 0
        ) {
          const cat = response.data.me.Dernier_Enregistrement[0];
          this.categories.push({
            id: cat.id,
            designation: cat.designation,
            description: cat.description,
            date_creation: cat.date_creation,
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
    // Suppression d'une catégorie via l'API
    async deleteCategorie(id) {
      this.loading = true;
      this.error = null;
      try {
        const formData = new FormData();
        formData.append("id", id);

        const response = await axios.post(
          "http://localhost/Api_Stock/categorie/delete/?user=herva&mdp=mdp",
          formData
        );

        if (
          response.data.me &&
          response.data.me.Reussite === "Catégorie supprimée avec succès"
        ) {
          this.categories = this.categories.filter(cat => cat.id !== id);
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
