import { defineStore } from 'pinia';
import axios from 'axios';

export const useClientStore = defineStore('client', {
  state: () => ({
    clients: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchClients() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          'http://localhost/Api_Stock/client/select/?user=herva&mdp=mdp'
        );

        // Transformation des données pour ignorer les propriétés numérotées
        this.clients = (response.data?.data || []).map(client => ({
          id: client.id,
          nom: client.nom || '',
          prenom: client.prenom || '',
          sexe: client.sexe || '',
          adresse: client.adresse || null,
          telephone: client.telephone || '',
          email: client.email || '',
          date_creation: client.date_creation || ''
        }));

      } catch (err) {
        this.error = err;
        this.clients = [];
        console.error('Erreur lors du chargement des clients:', err);
      } finally {
        this.loading = false;
      }
    },

    async saveClient(clientData) {
      this.loading = true;
      try {
        const response = await axios.post(
          'http://localhost/Api_Stock/client/save/?user=herva&mdp=mdp',
          clientData
        );
        await this.fetchClients(); // Rafraîchir la liste
        return response.data;
      } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateClient(clientData) {
      this.loading = true;
      try {
        const response = await axios.post(
          'http://localhost/Api_Stock/client/update/?user=herva&mdp=mdp',
          clientData
        );
        await this.fetchClients(); // Rafraîchir la liste
        return response.data;
      } catch (error) {
        console.error('Erreur lors de la mise à jour:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteClient(id) {
      this.loading = true;
      try {
        const response = await axios.post(
          'http://localhost/Api_Stock/client/delete/?user=herva&mdp=mdp',
          { id }
        );
        await this.fetchClients(); // Rafraîchir la liste
        return response.data;
      } catch (error) {
        console.error('Erreur lors de la suppression:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    }
  },

  getters: {
    clientOptions: (state) => {
      return state.clients.map(client => ({
        ...client,
        fullName: `${client.prenom} ${client.nom}`.trim(),
        label: `${client.prenom} ${client.nom} (${client.telephone})`.trim(),
        value: client.id
      }));
    },

    // Getter pour trouver un client par son ID
    getClientById: (state) => (id) => {
      return state.clients.find(client => client.id === id) || null;
    }
  }
});
