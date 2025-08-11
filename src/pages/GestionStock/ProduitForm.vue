<template>
  <div class="q-pa-md approvisionnement-form-bg">
    <q-card class="q-pa-lg approvisionnement-form-card">
      <!-- Bandeau titre -->
      <div class="approvisionnement-title-bar q-pa-md q-mb-md row items-center justify-between">
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="inventory_2" class="q-mr-sm" /> Gestion des Approvisionnements
        </div>
        <div class="text-white">
          Connecté en tant que: {{ user.nom || user.id }}
        </div>
      </div>

      <q-card-section>
        <div class="row items-center justify-between">
          <div class="text-h5 text-primary text-weight-bold">
            Liste des Approvisionnements
          </div>
          <q-btn
            color="primary"
            icon="add"
            label="Nouvel Approvisionnement"
            @click="showAddModal = true"
            :disable="loading"
          />
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <q-table
          :rows="approvisionnements"
          :columns="columns"
          row-key="id_approvisionnement"
          flat
          bordered
          :loading="loading"
          :pagination="{ rowsPerPage: 10 }"
          no-data-label="Aucun approvisionnement enregistré"
        >
          <!-- Colonne Produit -->
          <template #body-cell-produit="props">
            <q-td :props="props">
              {{ props.row.produit_nom || 'N/A' }} (ID: {{ props.row.id_produit }})
            </q-td>
          </template>

          <!-- Colonne Fournisseur -->
          <template #body-cell-fournisseur="props">
            <q-td :props="props">
              {{ props.row.nom_fournisseur || 'N/A' }}
            </q-td>
          </template>

          <!-- Colonne Date -->
          <template #body-cell-date="props">
            <q-td :props="props">
              {{ formatDate(props.row.date_approvisionnement) }}
            </q-td>
          </template>

          <!-- Colonne Actions -->
          <template #body-cell-actions="props">
            <q-td align="center">
              <q-btn
                dense
                flat
                round
                icon="edit"
                color="primary"
                @click="editApprovisionnement(props.row)"
                :disable="loading"
              />
              <q-btn
                dense
                flat
                round
                icon="delete"
                color="negative"
                @click="confirmDelete(props.row.id_approvisionnement)"
                :disable="loading"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Modal d'ajout -->
      <q-dialog v-model="showAddModal" persistent>
        <q-card style="min-width: 500px">
          <q-card-section>
            <div class="text-h6">Nouvel Approvisionnement</div>
            <div class="text-caption">Utilisateur: {{ user.nom || user.id }}</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="onSubmit" class="q-gutter-md">
              <!-- Sélecteur de produit -->
              <q-select
                v-model="newApprovisionnement.id_produit"
                :options="produitOptions"
                label="Produit *"
                option-value="id"
                option-label="designation"
                outlined
                dense
                color="primary"
                emit-value
                map-options
                :loading="categorieStore.loading"
                :rules="[val => !!val || 'Champ obligatoire']"
              />

              <!-- Sélecteur de fournisseur -->
              <q-select
                v-model="newApprovisionnement.id_fournisseur"
                :options="fournisseurOptions"
                label="Fournisseur *"
                option-value="id"
                option-label="nom"
                outlined
                dense
                color="primary"
                emit-value
                map-options
                :loading="fournisseurStore.loading"
                :rules="[val => !!val || 'Champ obligatoire']"
              />

              <q-input
                v-model.number="newApprovisionnement.quantite"
                label="Quantité *"
                type="number"
                outlined
                dense
                color="primary"
                min="1"
                :rules="[
                  val => val > 0 || 'Doit être positif',
                  val => !!val || 'Champ obligatoire'
                ]"
              />

              <q-input
                v-model.number="newApprovisionnement.prix_unitaire"
                label="Prix unitaire *"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
                step="0.01"
                :rules="[
                  val => val >= 0 || 'Doit être positif',
                  val => !!val || 'Champ obligatoire'
                ]"
              />

              <div class="row justify-end q-mt-md">
                <q-btn
                  label="Enregistrer"
                  color="primary"
                  type="submit"
                  icon="check"
                  :loading="loading"
                  unelevated
                />
                <q-btn
                  flat
                  label="Annuler"
                  color="grey"
                  class="q-ml-sm"
                  v-close-popup
                  :disable="loading"
                />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <!-- Modal de modification -->
      <q-dialog v-model="showEditModal" persistent>
        <q-card style="min-width: 500px">
          <q-card-section>
            <div class="text-h6">Modifier l'Approvisionnement #{{ editApprovisionnementData.id_approvisionnement }}</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="onUpdate" class="q-gutter-md">
              <q-input
                v-model="editApprovisionnementData.produit_nom"
                label="Produit"
                outlined
                dense
                readonly
              />

              <q-select
                v-model="editApprovisionnementData.id_fournisseur"
                :options="fournisseurOptions"
                label="Fournisseur *"
                option-value="id"
                option-label="nom"
                outlined
                dense
                color="primary"
                emit-value
                map-options
                :rules="[val => !!val || 'Champ obligatoire']"
              />

              <q-input
                v-model.number="editApprovisionnementData.quantite"
                label="Quantité *"
                type="number"
                outlined
                dense
                color="primary"
                min="1"
                :rules="[
                  val => val > 0 || 'Doit être positif',
                  val => !!val || 'Champ obligatoire'
                ]"
              />

              <q-input
                v-model.number="editApprovisionnementData.prix_unitaire"
                label="Prix unitaire *"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
                step="0.01"
                :rules="[
                  val => val >= 0 || 'Doit être positif',
                  val => !!val || 'Champ obligatoire'
                ]"
              />

              <div class="row justify-end q-mt-md">
                <q-btn
                  label="Mettre à jour"
                  color="primary"
                  type="submit"
                  icon="save"
                  :loading="loading"
                  unelevated
                />
                <q-btn
                  flat
                  label="Annuler"
                  color="grey"
                  class="q-ml-sm"
                  v-close-popup
                  :disable="loading"
                />
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
import { useApprovisionnementStore } from 'src/stores/ApprovisionnementStore';
import { useCategorieStore } from 'src/stores/ProduitStore';
import { useFournisseurStore } from 'src/stores/FournisseurStore';

const $q = useQuasar();

// Stores
const approvisionnementStore = useApprovisionnementStore();
const categorieStore = useCategorieStore();
const fournisseurStore = useFournisseurStore();

// Utilisateur connecté
const user = ref(JSON.parse(localStorage.getItem('user')) || { id: 1, nom: 'Administrateur' });

// États
const showAddModal = ref(false);
const showEditModal = ref(false);
const loading = ref(false);

// Formulaires
const newApprovisionnement = ref({
  id_produit: null,
  id_fournisseur: null,
  quantite: 1,
  prix_unitaire: 0,
  id_User: user.value.id
});

const editApprovisionnementData = ref({
  id_approvisionnement: null,
  id_produit: null,
  produit_nom: '',
  id_fournisseur: null,
  quantite: 1,
  prix_unitaire: 0
});

// Options pour les selects
const produitOptions = computed(() => {
  return (categorieStore.categories || []).map(p => ({
    id: p.id,
    designation: p.designation,
    description: p.description
  }));
});

const fournisseurOptions = computed(() => {
  return (fournisseurStore.fournisseurs || []).map(f => ({
    id: f.id,
    nom: f.nom,
    telephone: f.telephone
  }));
});

// Données à afficher
const approvisionnements = computed(() => {
  return approvisionnementStore.approvisionnements || [];
});

// Colonnes du tableau
const columns = [
  { name: 'id', label: 'ID', field: 'id_approvisionnement', align: 'left' },
  { name: 'produit', label: 'Produit', field: 'produit_nom', align: 'left' },
  { name: 'fournisseur', label: 'Fournisseur', field: 'nom_fournisseur', align: 'left' },
  { name: 'quantite', label: 'Quantité', field: 'quantite', align: 'right' },
  {
    name: 'prix',
    label: 'Prix Unitaire',
    field: 'prix_unitaire',
    align: 'right',
    format: val => `${parseFloat(val).toFixed(2)} $`
  },
  {
    name: 'date',
    label: 'Date',
    field: 'date_approvisionnement',
    align: 'right',
    format: val => formatDate(val)
  },
  { name: 'actions', label: 'Actions', align: 'center' }
];

// Méthodes
function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

async function onSubmit() {
  loading.value = true;
  try {
    const payload = {
      id_User: Number(user.value.id), // Maintenant avec U majuscule
      id_produit: Number(newApprovisionnement.value.id_produit),
      id_fournisseur: Number(newApprovisionnement.value.id_fournisseur),
      quantite: Number(newApprovisionnement.value.quantite),
      prix_unitaire: parseFloat(newApprovisionnement.value.prix_unitaire)
    };

    console.log('Données préparées:', payload);

    const response = await approvisionnementStore.saveApprovisionnement(payload);

    $q.notify({
      type: 'positive',
      message: response.message || 'Approvisionnement enregistré',
      timeout: 3000
    });

    showAddModal.value = false;
    resetForm();

  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.message,
      timeout: 5000
    });
  } finally {
    loading.value = false;
  }
}
async function onUpdate() {
  loading.value = true;
  try {
    // Validation renforcée
    const requiredFields = {
      'Fournisseur': editApprovisionnementData.value.id_fournisseur,
      'Quantité': editApprovisionnementData.value.quantite,
      'Prix unitaire': editApprovisionnementData.value.prix_unitaire
    };

    Object.entries(requiredFields).forEach(([field, value]) => {
      if (!value && value !== 0) throw new Error(`${field} est requis`);
      if (field === 'Quantité' && value <= 0) throw new Error("La quantité doit être positive");
      if (field === 'Prix unitaire' && value < 0) throw new Error("Le prix doit être positif");
    });

    // Préparation FormData pour l'API
    const formData = new FormData();
    formData.append('id_approvisionnement', editApprovisionnementData.value.id_approvisionnement);
    formData.append('id_User', user.value.id);
    formData.append('id_produit', editApprovisionnementData.value.id_produit);
    formData.append('id_fournisseur', editApprovisionnementData.value.id_fournisseur);
    formData.append('quantite', editApprovisionnementData.value.quantite);
    formData.append('prix_unitaire', editApprovisionnementData.value.prix_unitaire);

    // Debug: afficher le contenu de FormData
    for (let [key, value] of formData.entries()) {
      console.log(`${key}: ${value}`);
    }

    // Envoi à l'API
    const response = await approvisionnementStore.updateApprovisionnement(formData);

    if (!response?.succes) {
      throw new Error(response?.message || "Réponse invalide du serveur");
    }

    // Feedback utilisateur
    $q.notify({
      type: 'positive',
      message: response.message || "Modification enregistrée avec succès",
      timeout: 3000,
      position: 'top'
    });

    // Fermeture du modal et rafraîchissement
    showEditModal.value = false;
    await approvisionnementStore.fetchApprovisionnements();

  } catch (error) {
    console.error("Erreur détaillée:", {
      message: error.message,
      stack: error.stack,
      response: error.response?.data
    });

    $q.notify({
      type: 'negative',
      message: error.message || "Erreur technique lors de la modification",
      timeout: 5000,
      position: 'top',
      actions: [{ icon: 'close', color: 'white' }]
    });
  } finally {
    loading.value = false;
  }
}
async function confirmDelete(id) {
  $q.dialog({
    title: 'Confirmer la suppression',
    message: 'Êtes-vous sûr de vouloir supprimer cet approvisionnement ?',
    cancel: true,
    persistent: true,
    ok: {
      label: 'Supprimer',
      color: 'negative',
      loading: loading.value // Ajout du state de loading
    }
  }).onOk(async () => {
    loading.value = true;
    try {
      const result = await approvisionnementStore.deleteApprovisionnement(id);
      
      $q.notify({
        type: 'positive',
        message: result.message || 'Suppression réussie',
        timeout: 3000,
        position: 'top'
      });

      // Rechargement des données
      await approvisionnementStore.fetchApprovisionnements();

    } catch (error) {
      console.error('Erreur complète:', error);
      $q.notify({
        type: 'negative',
        message: error.message || 'Erreur inconnue lors de la suppression',
        timeout: 5000,
        position: 'top',
        actions: [{ icon: 'close', color: 'white' }]
      });
    } finally {
      loading.value = false;
    }
  });
}

function editApprovisionnement(item) {
  editApprovisionnementData.value = {
    id_approvisionnement: item.id_approvisionnement,
    id_produit: item.id_produit,
    produit_nom: item.produit_nom,
    id_fournisseur: item.id_fournisseur,
    quantite: item.quantite,
    prix_unitaire: item.prix_unitaire
  };
  showEditModal.value = true;
}

function resetForm() {
  newApprovisionnement.value = {
    id_produit: null,
    id_fournisseur: null,
    quantite: 1,
    prix_unitaire: 0,
    id_User: user.value.id
  };
}

// Initialisation
onMounted(async () => {
  loading.value = true;
  try {
    await Promise.all([
      approvisionnementStore.fetchApprovisionnements(),
      categorieStore.fetchCategories(),
      fournisseurStore.fetchFournisseurs()
    ]);
  } catch (error) {
    console.error("Erreur:", error);
    $q.notify({
      type: 'negative',
      message: "Erreur lors du chargement des données",
      timeout: 5000
    });
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.approvisionnement-form-bg {
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
  min-height: 100vh;
}

.approvisionnement-form-card {
  max-width: 1200px;
  margin: 0 auto;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.approvisionnement-title-bar {
  background: linear-gradient(90deg, #1976d2 0%, #2196f3 100%);
  border-radius: 10px 10px 0 0;
}

.q-table {
  font-size: 14px;
}

.q-table th {
  font-weight: 600;
}

.q-table td {
  padding: 8px 16px;
}

.q-table__top {
  padding: 12px 16px;
}
</style>
