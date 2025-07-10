<template>
  <div class="q-pa-md client-form-bg">
    <q-card class="q-pa-lg client-form-card">
      <!-- Bandeau titre -->
      <div class="client-title-bar q-pa-md q-mb-md row items-center justify-between">
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="people" class="q-mr-sm" /> Gestion des Clients
        </div>
        <div class="text-subtitle1 text-white">
          Total: {{ clients.length }} client(s)
        </div>
      </div>

      <q-card-section>
        <div class="row items-center justify-between">
          <div class="text-h5 text-primary text-weight-bold">Liste des Clients</div>
          <div>
            <q-btn color="primary" icon="refresh" label="Actualiser"
                   @click="fetchClients" class="q-mr-sm" :loading="loading" />
            <q-btn color="primary" icon="add" label="Ajouter Client"
                   @click="openAddModal" :loading="loading" />
          </div>
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <q-table
          :rows="clients"
          :columns="columns"
          row-key="id"
          flat
          bordered
          :loading="loading"
          :pagination="{ rowsPerPage: 10 }"
          no-data-label="Aucun client enregistré"
        >
          <template #body-cell-sex="props">
            <q-td :props="props">
              <q-icon :name="props.row.sexe === 'M' ? 'male' : 'female'"
                      :color="props.row.sexe === 'M' ? 'blue' : 'pink'" />
              {{ props.row.sexe === 'M' ? 'Homme' : 'Femme' }}
            </q-td>
          </template>

          <template #body-cell-contact="props">
            <q-td :props="props">
              <div>{{ props.row.telephone }}</div>
              <div class="text-caption">{{ props.row.email }}</div>
            </q-td>
          </template>

          <template #body-cell-actions="props">
            <q-td align="center" class="q-gutter-xs">
              <q-btn dense flat round icon="edit" color="primary"
                     @click="editClient(props.row)" :disable="loading" />
              <q-btn dense flat round icon="delete" color="negative"
                     @click="confirmDeleteClient(props.row.id)" :disable="loading" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Modal d'ajout -->
      <q-dialog v-model="showAddModal" persistent>
        <q-card style="min-width: 500px">
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Ajouter un Client</div>
            <q-space />
            <q-btn icon="close" flat round dense v-close-popup :disable="loading" />
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="addClient" class="q-gutter-md">
              <div class="row q-gutter-md">
                <q-input
                  class="col"
                  v-model.trim="newClient.nom"
                  label="Nom"
                  outlined
                  dense
                  lazy-rules
                  :rules="[val => !!val || 'Champ obligatoire']"
                />
                <q-input
                  class="col"
                  v-model.trim="newClient.prenom"
                  label="Prénom"
                  outlined
                  dense
                  lazy-rules
                  :rules="[val => !!val || 'Champ obligatoire']"
                />
              </div>

              <q-select
                v-model="newClient.sexe"
                :options="sexeOptions"
                label="Sexe"
                outlined
                dense
                emit-value
                map-options
                :rules="[val => !!val || 'Champ obligatoire']"
              />

              <q-input
                v-model.trim="newClient.email"
                label="Email"
                type="email"
                outlined
                dense
                lazy-rules
                :rules="[
                  val => !val || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'Email invalide'
                ]"
              />

              <q-input
                v-model.trim="newClient.telephone"
                label="Téléphone"
                outlined
                dense
                mask="##########"
                lazy-rules
                :rules="[
                  val => !!val || 'Champ obligatoire',
                  val => val.length === 10 || '10 chiffres requis'
                ]"
              />

              <q-input
                v-model.trim="newClient.adresse"
                label="Adresse"
                outlined
                dense
                type="textarea"
                autogrow
              />

              <div class="row justify-end q-mt-md">
                <q-btn label="Annuler" color="grey" v-close-popup :disable="loading" class="q-mr-sm" />
                <q-btn label="Enregistrer" color="primary" type="submit" :loading="loading" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <!-- Modal de modification -->
      <q-dialog v-model="showEditModal" persistent>
        <q-card style="min-width: 500px">
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Modifier le Client</div>
            <q-space />
            <q-btn icon="close" flat round dense v-close-popup :disable="loading" />
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="updateClient" class="q-gutter-md">
              <div class="row q-gutter-md">
                <q-input
                  class="col"
                  v-model.trim="selectedClient.nom"
                  label="Nom"
                  outlined
                  dense
                  lazy-rules
                  :rules="[val => !!val || 'Champ obligatoire']"
                />
                <q-input
                  class="col"
                  v-model.trim="selectedClient.prenom"
                  label="Prénom"
                  outlined
                  dense
                  lazy-rules
                  :rules="[val => !!val || 'Champ obligatoire']"
                />
              </div>

              <q-select
                v-model="selectedClient.sexe"
                :options="sexeOptions"
                label="Sexe"
                outlined
                dense
                emit-value
                map-options
                :rules="[val => !!val || 'Champ obligatoire']"
              />

              <q-input
                v-model.trim="selectedClient.email"
                label="Email"
                type="email"
                outlined
                dense
                lazy-rules
                :rules="[
                  val => !val || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'Email invalide'
                ]"
              />

              <q-input
                v-model.trim="selectedClient.telephone"
                label="Téléphone"
                outlined
                dense
                mask="##########"
                lazy-rules
                :rules="[
                  val => !!val || 'Champ obligatoire',
                  val => val.length === 10 || '10 chiffres requis'
                ]"
              />

              <q-input
                v-model.trim="selectedClient.adresse"
                label="Adresse"
                outlined
                dense
                type="textarea"
                autogrow
              />

              <div class="row justify-end q-mt-md">
                <q-btn label="Annuler" color="grey" v-close-popup :disable="loading" class="q-mr-sm" />
                <q-btn label="Mettre à jour" color="primary" type="submit" :loading="loading" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
    </q-card>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { useClientStore } from 'stores/ClientStore';

export default {
  setup() {
    const $q = useQuasar();
    const clientStore = useClientStore();

    const sexeOptions = [
      { label: 'Homme', value: 'M' },
      { label: 'Femme', value: 'F' }
    ];

    const newClient = ref({
      nom: '',
      prenom: '',
      sexe: 'M',
      email: '',
      telephone: '',
      adresse: ''
    });

    const selectedClient = ref({
      id: null,
      nom: '',
      prenom: '',
      sexe: 'M',
      email: '',
      telephone: '',
      adresse: ''
    });

    const showAddModal = ref(false);
    const showEditModal = ref(false);

    const columns = [
      { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
      { name: 'nom', label: 'Nom', field: 'nom', align: 'left', sortable: true },
      { name: 'prenom', label: 'Prénom', field: 'prenom', align: 'left', sortable: true },
      { name: 'sexe', label: 'Sexe', field: 'sexe', align: 'center', sortable: true },
      { name: 'contact', label: 'Contact', field: '', align: 'left' },
      { name: 'adresse', label: 'Adresse', field: 'adresse', align: 'left' },
      { name: 'actions', label: 'Actions', align: 'center', sortable: false }
    ];

    async function addClient() {
      try {
        await clientStore.saveClient(newClient.value);
        $q.notify({
          type: 'positive',
          message: 'Client ajouté avec succès',
          icon: 'check_circle',
          timeout: 2000
        });
        resetNewClient();
        showAddModal.value = false;
      } catch (error) {
        console.error('Erreur:', error);
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erreur lors de l\'ajout du client',
          icon: 'error',
          timeout: 3000
        });
      }
    }

    function openAddModal() {
      resetNewClient();
      showAddModal.value = true;
    }

    function editClient(client) {
      selectedClient.value = { ...client };
      showEditModal.value = true;
    }

    async function updateClient() {
      try {
        await clientStore.updateClient(selectedClient.value);
        $q.notify({
          type: 'positive',
          message: 'Client mis à jour avec succès',
          icon: 'check_circle',
          timeout: 2000
        });
        showEditModal.value = false;
      } catch (error) {
        console.error('Erreur:', error);
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erreur lors de la mise à jour du client',
          icon: 'error',
          timeout: 3000
        });
      }
    }

    function confirmDeleteClient(id) {
      $q.dialog({
        title: 'Confirmer la suppression',
        message: 'Êtes-vous sûr de vouloir supprimer ce client ?',
        cancel: true,
        persistent: true,
        ok: {
          label: 'Supprimer',
          color: 'negative',
          flat: true
        },
        cancel: {
          label: 'Annuler',
          color: 'primary',
          flat: true
        }
      }).onOk(async () => {
        await deleteClient(id);
      });
    }

    async function deleteClient(id) {
      try {
        await clientStore.deleteClient(id);
        $q.notify({
          type: 'positive',
          message: 'Client supprimé avec succès',
          icon: 'check_circle',
          timeout: 2000
        });
      } catch (error) {
        console.error('Erreur:', error);
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erreur lors de la suppression du client',
          icon: 'error',
          timeout: 3000
        });
      }
    }

    function resetNewClient() {
      newClient.value = {
        nom: '',
        prenom: '',
        sexe: 'M',
        email: '',
        telephone: '',
        adresse: ''
      };
    }

    async function fetchClients() {
      try {
        await clientStore.fetchClients();
      } catch (error) {
        console.error('Erreur:', error);
        $q.notify({
          type: 'negative',
          message: 'Erreur lors du chargement des clients',
          icon: 'error',
          timeout: 3000
        });
      }
    }

    onMounted(() => {
      fetchClients();
    });

    return {
      clients: computed(() => clientStore.clients),
      loading: computed(() => clientStore.loading),
      newClient,
      selectedClient,
      showAddModal,
      showEditModal,
      columns,
      sexeOptions,
      addClient,
      editClient,
      updateClient,
      deleteClient,
      confirmDeleteClient,
      fetchClients,
      openAddModal
    };
  }
};
</script>

<style scoped>
.client-form-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.client-form-card {
  max-width: 100%;
  width: 100%;
  margin: auto;
  border-radius: 18px;
  box-shadow: 0 8px 32px 0 rgba(0,0,0,0.10), 0 1.5px 4px 0 rgba(0,0,0,0.08);
}
.client-title-bar {
  background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
  border-radius: 14px 14px 0 0;
  text-align: left;
  display: flex;
  align-items: center;
}
</style>
