<template>
  <div class="q-pa-md approvisionnement-form-bg">
    <q-card class="q-pa-lg approvisionnement-form-card">
      <!-- Bandeau titre -->
      <div class="approvisionnement-title-bar q-pa-md q-mb-md row items-center justify-between">
        <div class="text-h4 text-blue text-weight-bold">
          <q-icon name="inventory_2" class="q-mr-sm" /> Gestion des Approvisionnements
        </div>
        <div class="text-white">
          Connecté en tant que: {{ user.nom || user.id }}
        </div>
      </div>

      <!-- Notifications -->
      <transition-group name="notification">
        <q-banner v-if="successMessage" key="success" class="bg-positive text-white q-mb-md">
          <template v-slot:avatar>
            <q-icon name="check_circle" />
          </template>
          {{ successMessage }}
          <template v-slot:action>
            <q-btn flat color="white" icon="close" @click="successMessage = ''" />
          </template>
        </q-banner>

        <q-banner v-if="errorMessage" key="error" class="bg-negative text-white q-mb-md">
          <template v-slot:avatar>
            <q-icon name="error" />
          </template>
          {{ errorMessage }}
          <template v-slot:action>
            <q-btn flat color="white" icon="close" @click="errorMessage = ''" />
          </template>
        </q-banner>

        <!-- Alerte stock faible -->
        <q-banner
          v-if="lowStockProducts.length > 0"
          key="lowStock"
          class="bg-orange text-white q-mb-md"
        >
          <template v-slot:avatar>
            <q-icon name="warning" />
          </template>
          <strong>ALERTE STOCK FAIBLE :</strong>
          Les produits suivants doivent être réapprovisionnés (stock ≤ 5) :
          <span v-for="(product, index) in lowStockProducts" :key="product.id">
            {{ product.designation }} ({{ product.stock_restant }})
            {{ index < lowStockProducts.length - 1 ? ',' : '' }}
          </span>
          <template v-slot:action>
            <q-btn flat color="white" icon="close" @click="dismissLowStockAlert" />
          </template>
        </q-banner>
      </transition-group>
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
import { ref, computed, onMounted, watch } from 'vue';
import { useQuasar } from 'quasar';
import { useApprovisionnementStore } from 'src/stores/ApprovisionnementStore';
import { useCategorieStore } from 'src/stores/ProduitStore';
import { useFournisseurStore } from 'src/stores/FournisseurStore';
import { useStockStore } from 'src/stores/stockStore';

const $q = useQuasar();

// Stores
const approvisionnementStore = useApprovisionnementStore();
const categorieStore = useCategorieStore();
const fournisseurStore = useFournisseurStore();
const stockStore = useStockStore();

// Utilisateur connecté
const user = ref(JSON.parse(localStorage.getItem('user')) || { id: 1, nom: 'Administrateur' });

// États
const showAddModal = ref(false);
const showEditModal = ref(false);
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const showLowStockAlert = ref(true); // Contrôle l'affichage de l'alerte

// Produits en stock faible
const lowStockProducts = computed(() => {
  if (!stockStore.stockData) return [];

  return stockStore.stockData
    .filter(item => item.stock_restant_m3 <= 5)
    .map(item => ({
      id: item.id_produit,
      designation: item.produit,
      stock_restant: item.stock_restant_m3.toFixed(3) + ' m³'
    }));
});

// Méthode pour masquer l'alerte
const dismissLowStockAlert = () => {
  showLowStockAlert.value = false;
};

// Surveiller les changements de stock
watch(() => stockStore.stockData, (newVal) => {
  if (newVal && newVal.some(item => item.stock_restant_m3 <= 5)) {
    showLowStockAlert.value = true;

    // Notification push si stock très faible (≤2)
    const criticalStock = newVal.filter(item => item.stock_restant_m3 <= 2);
    if (criticalStock.length > 0) {
      $q.notify({
        type: 'negative',
        message: `STOCK CRITIQUE pour ${criticalStock.map(p => p.produit).join(', ')}`,
        timeout: 0, // Ne pas disparaître automatiquement
        position: 'top-right',
        actions: [
          {
            label: 'Voir',
            color: 'white',
            handler: () => {
              // Rediriger vers la page de réapprovisionnement
              // (à adapter selon votre routing)
            }
          }
        ]
      });
    }
  }
}, { deep: true });

// Initialisation
onMounted(async () => {
  loading.value = true;
  try {
    await Promise.all([
      approvisionnementStore.fetchApprovisionnements(),
      categorieStore.fetchCategories(),
      fournisseurStore.fetchFournisseurs(),
      stockStore.fetchStockData({
        username: user.value.username,
        password: 'mdp' // À remplacer par le mot de passe sécurisé
      })
    ]);
  } catch (error) {
    errorMessage.value = "Erreur lors du chargement des données";
    console.error("Erreur:", error);
  } finally {
    loading.value = false;
  }
});

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
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const payload = {
      id_User: Number(user.value.id),
      id_produit: Number(newApprovisionnement.value.id_produit),
      id_fournisseur: Number(newApprovisionnement.value.id_fournisseur),
      quantite: Number(newApprovisionnement.value.quantite),
      prix_unitaire: parseFloat(newApprovisionnement.value.prix_unitaire)
    };

    const result = await approvisionnementStore.saveApprovisionnement(payload);

    if (result.success) {
      successMessage.value = result.message;
      showAddModal.value = false;
      resetForm();
      await approvisionnementStore.fetchApprovisionnements();
    } else {
      throw new Error(result.message || "Erreur inconnue");
    }

  } catch (error) {
    errorMessage.value = error.message;
    console.error("Erreur détaillée:", {
      message: error.message,
      stack: error.stack,
      response: error.response?.data
    });
  } finally {
    loading.value = false;
  }
}
async function onUpdate() {
  loading.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    // Préparation des données pour le store
    const updateData = {
      id_approvisionnement: editApprovisionnementData.value.id_approvisionnement,
      id_produit: editApprovisionnementData.value.id_produit,
      id_fournisseur: editApprovisionnementData.value.id_fournisseur,
      quantite: editApprovisionnementData.value.quantite,
      prix_unitaire: editApprovisionnementData.value.prix_unitaire,
      id_User: user.value.id
    };

    console.log("Données envoyées:", updateData); // Debug

    const result = await approvisionnementStore.updateApprovisionnement(updateData);

    if (result.success) {
      successMessage.value = result.message;
      showEditModal.value = false;
      await approvisionnementStore.fetchApprovisionnements();
    } else {
      throw new Error(result.message || "Erreur inconnue");
    }
  } catch (error) {
    errorMessage.value = error.message;
    console.error("Erreur détaillée:", {
      message: error.message,
      stack: error.stack,
      response: error.response?.data
    });

    // Notification Quasar
    $q.notify({
      type: 'negative',
      message: error.message,
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
      color: 'negative'
    }
  }).onOk(async () => {
    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
      const result = await approvisionnementStore.deleteApprovisionnement(id);

      if (result.success) {
        successMessage.value = result.message;
        await approvisionnementStore.fetchApprovisionnements();
      } else {
        throw new Error(result.message || "Erreur inconnue");
      }

    } catch (error) {
      errorMessage.value = error.message || "Erreur lors de la suppression";
      console.error("Erreur complète:", {
        message: error.message,
        stack: error.stack,
        response: error.response?.data
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
    errorMessage.value = "Erreur lors du chargement des données";
    console.error("Erreur:", error);
  } finally {
    loading.value = false;
  }
});
</script>
