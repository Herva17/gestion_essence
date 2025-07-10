import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    loading: false,
    error: null,
    isAuthenticated: false,
  }),
  actions: {
    async login({ email, mot_de_passe }) {
      this.loading = true;
      this.error = null;
      try {
        let formData = new FormData();
        formData.append("email", email);
        formData.append("mot_de_passe", mot_de_passe);

        const response = await axios.post(
          "http://localhost/Api_Stock/utilisateur/connexion/?user=herva&mdp=mdp",
          formData,
          { headers: { "Content-Type": "multipart/form-data" } }
        );

        // Correction ici : on teste "succes" et "utilisateur"
        if (response.data && response.data.succes && response.data.utilisateur) {
          this.user = response.data.utilisateur;
          this.isAuthenticated = true;
        } else {
          this.user = null;
          this.isAuthenticated = false;
        }
        this.loading = false;
        return response.data;
      } catch (err) {
        this.error = err;
        this.isAuthenticated = false;
        this.loading = false;
        throw err;
      }
    },
    logout() {
      this.user = null;
      this.isAuthenticated = false;
    },
  },
});
