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
        const formData = new FormData();
        // S'assurer que le champ "sexe" est bien transmis et non vide
        Object.entries(clientData).forEach(([k, v]) => {
          if (k === 'sexe') {
            formData.append('sexe', v ? v : 'M');
          } else {
            formData.append(k, v);
          }
        });
        // Debug : voir ce qui est envoyé
        // for (let pair of formData.entries()) { console.log(pair[0]+ ': ' + pair[1]); }
        const response = await axios.post(
          'http://localhost/Api_Stock/client/save/?user=herva&mdp=mdp',
          formData
        );
        await this.fetchClients();
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
        const formData = new FormData();
        Object.entries(clientData).forEach(([k, v]) => {
          if (k === 'sexe') {
            formData.append('sexe', v ? v : 'M');
          } else {
            formData.append(k, v);
          }
        });
        // Debug : voir ce qui est envoyé
        // for (let pair of formData.entries()) { console.log(pair[0]+ ': ' + pair[1]); }
        const response = await axios.post(
          'http://localhost/Api_Stock/client/update/?user=herva&mdp=mdp',
          formData
        );
        await this.fetchClients();
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
        const formData = new FormData();
        formData.append('id', id);
        const response = await axios.post(
          'http://localhost/Api_Stock/client/delete/?user=herva&mdp=mdp',
          formData
        );
        await this.fetchClients();
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

    getClientById: (state) => (id) => {
      return state.clients.find(client => client.id === id) || null;
    }
  }
});
