<template>
  <div class="q-pa-md client-form-bg">
    <q-card class="q-pa-lg client-form-card">
      <!-- Bandeau titre -->
      <div class="client-title-bar q-pa-md q-mb-md row items-center justify-between">
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="people" class="q-mr-sm" /> Gestion des Clients
        </div>
      </div>
      <!-- Fin bandeau titre -->

      <q-card-section>
        <div class="row items-center justify-between">
          <div class="text-h5 text-primary text-weight-bold">Liste des Clients</div>
          <q-btn color="primary" icon="add" label="Ajouter Client" @click="showAddModal = true" />
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
          :pagination="{ rowsPerPage: 10 }"
          no-data-label="Aucun client enregistré"
        >
          <template #body-cell-actions="props">
            <q-td align="center">
              <q-btn dense flat round icon="edit" color="primary" @click="editClient(props.row)" />
              <q-btn dense flat round icon="delete" color="negative" @click="deleteClient(props.row.id)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Modal d'ajout -->
      <q-dialog v-model="showAddModal">
        <q-card style="min-width:400px">
          <q-card-section>
            <div class="text-h6">Ajouter un Client</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onSubmit" class="q-gutter-md">
              <div class="row q-gutter-md">
                <q-input
                  v-model="client.nom"
                  label="Nom"
                  outlined
                  dense
                  required
                  color="primary"
                  class="col"
                />
                <q-input
                  v-model="client.prenom"
                  label="Prénom"
                  outlined
                  dense
                  required
                  color="primary"
                  class="col"
                />
              </div>

              <q-select
                v-model="client.sexe"
                :options="sexeOptions"
                label="Sexe"
                outlined
                dense
                color="primary"
                emit-value
                map-options
              />

              <q-input
                v-model="client.adresse"
                label="Adresse"
                outlined
                dense
                color="primary"
                type="textarea"
                autogrow
              />

              <div class="row q-gutter-md">
                <q-input
                  v-model="client.telephone"
                  label="Téléphone"
                  outlined
                  dense
                  color="primary"
                  mask="### ### ###"
                  class="col"
                />
                <q-input
                  v-model="client.email"
                  label="Email"
                  outlined
                  dense
                  color="primary"
                  type="email"
                  class="col"
                />
              </div>

              <div class="row justify-end">
                <q-btn label="Enregistrer" color="primary" type="submit" icon="check" unelevated />
                <q-btn flat label="Annuler" color="grey" class="q-ml-sm" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <!-- Modal de modification -->
      <q-dialog v-model="showEditModal">
        <q-card style="min-width:400px">
          <q-card-section>
            <div class="text-h6">Modifier le Client</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onUpdate" class="q-gutter-md">
              <div class="row q-gutter-md">
                <q-input
                  v-model="editClientData.nom"
                  label="Nom"
                  outlined
                  dense
                  required
                  color="primary"
                  class="col"
                />
                <q-input
                  v-model="editClientData.prenom"
                  label="Prénom"
                  outlined
                  dense
                  required
                  color="primary"
                  class="col"
                />
              </div>

              <q-select
                v-model="editClientData.sexe"
                :options="sexeOptions"
                label="Sexe"
                outlined
                dense
                color="primary"
                emit-value
                map-options
              />

              <q-input
                v-model="editClientData.adresse"
                label="Adresse"
                outlined
                dense
                color="primary"
                type="textarea"
                autogrow
              />

              <div class="row q-gutter-md">
                <q-input
                  v-model="editClientData.telephone"
                  label="Téléphone"
                  outlined
                  dense
                  color="primary"
                  mask="### ### ###"
                  class="col"
                />
                <q-input
                  v-model="editClientData.email"
                  label="Email"
                  outlined
                  dense
                  color="primary"
                  type="email"
                  class="col"
                />
              </div>

              <div class="row justify-end">
                <q-btn label="Mettre à jour" color="primary" type="submit" icon="save" unelevated />
                <q-btn flat label="Annuler" color="grey" class="q-ml-sm" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
    </q-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { useClientStore } from 'src/stores/ClientStore';

const $q = useQuasar();
const clientStore = useClientStore();

const showAddModal = ref(false);
const showEditModal = ref(false);

const client = ref({
  nom: '',
  prenom: '',
  sexe: '',
  adresse: '',
  telephone: '',
  email: '',
});

const editClientData = ref({
  id: null,
  nom: '',
  prenom: '',
  sexe: '',
  adresse: '',
  telephone: '',
  email: '',
});

const sexeOptions = [
  { label: 'Masculin', value: 'M' },
  { label: 'Féminin', value: 'F' },
  { label: 'Autre', value: 'A' }
];

// Utilise les clients du store
const clients = computed(() => clientStore.clients);

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'nom', label: 'Nom', field: 'nom', align: 'left' },
  { name: 'prenom', label: 'Prénom', field: 'prenom', align: 'left' },
  { name: 'sexe', label: 'Sexe', field: 'sexe', align: 'center', format: val => val === 'M' ? 'Homme' : val === 'F' ? 'Femme' : 'Autre' },
  { name: 'telephone', label: 'Téléphone', field: 'telephone', align: 'left' },
  { name: 'email', label: 'Email', field: 'email', align: 'left' },
  { name: 'date_creation', label: 'Date Création', field: 'date_creation', align: 'right' },
  { name: 'actions', label: 'Actions', field: 'actions', align: 'center', sortable: false }
];

onMounted(() => {
  clientStore.fetchClients();
});

async function onSubmit() {
  try {
    await clientStore.saveClient({
      nom: client.value.nom,
      prenom: client.value.prenom,
      sexe: client.value.sexe,
      adresse: client.value.adresse,
      telephone: client.value.telephone,
      email: client.value.email,
    });
    $q.notify({ type: 'positive', message: 'Client enregistré !' });
    client.value = { nom: '', prenom: '', sexe: '', adresse: '', telephone: '', email: '' };
    showAddModal.value = false;
  } catch (e) {
    $q.notify({ type: 'negative', message: "Erreur lors de l'enregistrement !" });
  }
}

async function deleteClient(id) {
  try {
    await clientStore.deleteClient(id);
    $q.notify({ type: 'negative', message: 'Client supprimé.' });
  } catch (e) {
    $q.notify({ type: 'negative', message: "Erreur lors de la suppression !" });
  }
}

function editClient(row) {
  editClientData.value = { ...row };
  showEditModal.value = true;
}

async function onUpdate() {
  try {
    await clientStore.updateClient(editClientData.value);
    $q.notify({ type: 'positive', message: 'Client modifié !' });
    showEditModal.value = false;
  } catch (e) {
    $q.notify({ type: 'negative', message: "Erreur lors de la modification !" });
  }
}
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
  background: linear-gradient(90deg, #059669 0%, #10b981 100%);
  border-radius: 14px 14px 0 0;
  text-align: left;
  display: flex;
  align-items: center;
}
</style>
