import { defineStore } from "pinia";
import axios from "axios";

export const useMouvementStore = defineStore("mouvement", {
  state: () => ({
    mouvements: [],
    totalMouvements: 0, // Ajout du compteur
    loading: false,
    error: null,
  }),
  actions: {
    async fetchMouvements() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/mouvement/lister/?user=herva&mdp=mdp"
        );
        this.mouvements = (response.data.data || []).map(mvt => ({
          id: mvt.id,
          designation: mvt.designation,
          quantite: mvt.Quantite,
          prix_unitaire: mvt.Prix_Unitaire,
          type: mvt.type,
          date_mouvement: mvt.date_mouvement
        }));
        this.loading = false;
      } catch (err) {
        this.error = err;
        this.loading = false;
      }
    },
    async fetchTotalMouvements() {
      try {
        const response = await axios.get(
          "http://localhost/Api_Stock/mouvement/compter/?user=herva&mdp=mdp"
        );
        this.totalMouvements = response.data.total || 0;
      } catch (err) {
        this.totalMouvements = 0;
      }
    }
  },
});
