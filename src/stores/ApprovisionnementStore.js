import { defineStore } from "pinia";
import axios from "axios";

// Configuration de base
const API_BASE_URL = "http://localhost/Api_Stock/approvisionnement";
const API_CREDENTIALS = "?user=herva&mdp=mdp";

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
        const response = await axios.get(`${API_BASE_URL}/select/${API_CREDENTIALS}`);

        if (response.data?.success === false) {
          throw new Error(response.data.message || "Erreur serveur");
        }

        this.approvisionnements = response.data?.data || [];
        this.lastFetch = new Date();
        return this.approvisionnements;
      } catch (err) {
        this.error = err.message || "Erreur lors du chargement";
        console.error("fetchApprovisionnements error:", {
          error: err,
          response: err.response?.data
        });
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async countApprovisionnements() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`${API_BASE_URL}/compter/${API_CREDENTIALS}`);

        if (!response.data?.succes) {
          throw new Error(response.data?.message || "Format de réponse invalide");
        }

        const total = response.data.data?.total?.[0]?.total ||
                     response.data.data?.[0]?.total ||
                     response.data.total ||
                     0;

        this.totalApprovisionnements = Number(total) || 0;
        return this.totalApprovisionnements;
      } catch (err) {
        this.error = err.message || "Erreur lors du comptage";
        console.error("countApprovisionnements error:", {
          error: err,
          response: err.response?.data
        });
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
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );

    // Modification ici pour gérer différentes structures de réponse
    const success = response.data?.success || response.data?.succes;
    if (!success) {
      throw new Error(response.data?.message || "Erreur lors de l'enregistrement");
    }

    await Promise.all([
      this.fetchApprovisionnements(true),
      this.countApprovisionnements()
    ]);

    // Retourner les données avec un flag de succès
    return {
      success: true,
      message: response.data?.message || "Approvisionnement enregistré",
      data: response.data
    };

  } catch (err) {
    console.error("Détails de l'erreur:", {
      error: err,
      response: err.response?.data
    });
    throw new Error(err.message || "Erreur lors de l'enregistrement");
  } finally {
    this.loading = false;
  }
},

   async updateApprovisionnement(approData) {
  this.loading = true;
  this.error = null;
  try {
    // Vérification des champs avec gestion spéciale pour FormData
    const requiredFields = {
      id_approvisionnement: approData.id_approvisionnement,
      id_produit: approData.id_produit,
      id_fournisseur: approData.id_fournisseur,
      quantite: approData.quantite,
      prix_unitaire: approData.prix_unitaire
    };

    const missingFields = Object.entries(requiredFields)
      .filter(([key, value]) => value === undefined || value === null || value === '')
      .map(([key]) => key);

    if (missingFields.length > 0) {
      throw new Error(`Champs obligatoires manquants: ${missingFields.join(', ')}`);
    }

    // Création du FormData
    const formData = new FormData();
    formData.append('id_approvisionnement', approData.id_approvisionnement);
    formData.append('id_User', approData.id_User || user.value.id);
    formData.append('id_produit', approData.id_produit);
    formData.append('id_fournisseur', approData.id_fournisseur);
    formData.append('quantite', approData.quantite);
    formData.append('prix_unitaire', approData.prix_unitaire);

    // Debug: Afficher le contenu de FormData
    for (let [key, value] of formData.entries()) {
      console.log(`FormData: ${key} = ${value}`);
    }

    const response = await axios.post(
      `${API_BASE_URL}/update/${API_CREDENTIALS}`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );

    // Gestion des différentes orthographes de 'success'
    const isSuccess = response.data?.success || response.data?.succes;

    if (!isSuccess) {
      throw new Error(response.data?.message || "Erreur lors de la modification");
    }

    await Promise.all([
      this.fetchApprovisionnements(true),
      this.countApprovisionnements()
    ]);

    return {
      success: true,
      message: response.data?.message || "Modification enregistrée",
      data: response.data
    };

  } catch (err) {
    console.error("Erreur de modification:", {
      error: err,
      inputData: approData,
      response: err.response?.data
    });
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
      `${API_BASE_URL}/delete/${API_CREDENTIALS}`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );

    console.log("Réponse complète de l'API:", response.data); // Log pour débogage

    // Vérification plus robuste du succès
    const isSuccess = response.data?.success !== false &&
                     response.data?.succes !== false &&
                     !response.data?.error;

    if (!isSuccess) {
      const errorMsg = response.data?.message ||
                      response.data?.error ||
                      "Erreur lors de la suppression";
      throw new Error(errorMsg);
    }

    await Promise.all([
      this.fetchApprovisionnements(true),
      this.countApprovisionnements()
    ]);

    return {
      success: true,
      message: response.data?.message || "Suppression réussie",
      data: response.data
    };

  } catch (err) {
    console.error("Erreur détaillée:", {
      error: err,
      id: id,
      response: err.response?.data,
      config: err.config
    });
    throw new Error(err.message || "Erreur technique lors de la suppression");
  } finally {
    this.loading = false;
  }
},
    // Méthodes utilitaires
    validateRequiredFields(data, fields) {
      const missingFields = fields.filter(field => {
        const value = data[field];
        return value === undefined || value === null || value === '';
      });

      if (missingFields.length > 0) {
        throw new Error(`Champs obligatoires manquants: ${missingFields.join(', ')}`);
      }
    },

    validateFieldTypes(data, fieldTypes) {
      const typeErrors = [];

      for (const [field, expectedType] of Object.entries(fieldTypes)) {
        const value = data[field];
        const actualType = typeof value;

        if (actualType !== expectedType) {
          typeErrors.push(`${field}: attendu ${expectedType}, reçu ${actualType}`);
        }
      }

      if (typeErrors.length > 0) {
        throw new Error(`Erreurs de type: ${typeErrors.join('; ')}`);
      }
    },

    formatErrorMessage(err) {
      if (err.response?.data?.message) {
        return err.response.data.message;
      }
      return err.message || "Une erreur inconnue est survenue";
    }
  },

  getters: {
    getById: (state) => (id) =>
      state.approvisionnements.find(a => a.id_approvisionnement === id),

    getByProduit: (state) => (id_produit) =>
      state.approvisionnements.filter(a => a.id_produit === id_produit),

    getByFournisseur: (state) => (id_fournisseur) =>
      state.approvisionnements.filter(a => a.id_fournisseur === id_fournisseur),

    getTotalApprovisionnements: (state) => state.totalApprovisionnements,
    isLoading: (state) => state.loading,
    getError: (state) => state.error
  }
});
