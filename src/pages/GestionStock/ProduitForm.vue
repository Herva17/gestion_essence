<template>
  <div class="q-pa-md produit-form-bg">
    <q-card class="q-pa-lg produit-form-card">
      <!-- Bandeau titre -->
      <div class="produit-title-bar q-pa-md q-mb-md row items-center justify-between">
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="inventory_2" class="q-mr-sm" /> Gestion des Produits
        </div>
        <!-- Icône notification -->
        <q-btn
          flat
          round
          dense
          icon="notifications"
          color="white"
          @click="showNotif"
          :class="{ 'bg-red-3': hasStockAlert }"
        >
          <q-badge v-if="hasStockAlert" color="red" floating rounded />
        </q-btn>
      </div>
      <!-- Fin bandeau titre -->

      <q-card-section>
        <div class="row items-center justify-between">
          <div class="text-h5 text-primary text-weight-bold">Liste des Produits</div>
          <q-btn color="primary" icon="add" label="Ajouter Produit" @click="showAddModal = true" />
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-table
          :rows="produits"
          :columns="columns"
          row-key="id"
          flat
          bordered
          :pagination="{ rowsPerPage: 5 }"
          no-data-label="Aucun produit enregistré"
        >
          <!-- Quantité en rouge si < 80 -->
          <template #body-cell-quantite="props">
            <q-td :props="props">
              <span :style="{ color: props.row.quantite <= 10 ? 'red' : 'inherit', 'font-weight': props.row.quantite < 80 ? 'bold' : 'normal' }">
                {{ props.row.quantite }}
              </span>
            </q-td>
          </template>
          <template #body-cell-actions="props">
            <q-td align="center">
              <q-btn dense flat round icon="edit" color="primary" @click="editProduit(props.row)" />
              <q-btn dense flat round icon="delete" color="negative" @click="deleteProduit(props.row.id)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Modal d'ajout -->
      <q-dialog v-model="showAddModal">
        <q-card style="min-width:400px">
          <q-card-section>
            <div class="text-h6">Ajouter un Produit</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onSubmit" class="q-gutter-md">
              <q-input
                v-model="produit.nom"
                label="Nom du produit"
                outlined
                dense
                required
                color="primary"
              />
              <q-input
                v-model="produit.description"
                label="Description"
                outlined
                dense
                color="primary"
              />
              <q-input
                v-model="produit.quantite"
                label="Quantité"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
                :input-style="quantiteInputStyle(produit.quantite)"
              />
              <q-input
                v-model="produit.prix_unitaire"
                label="Prix unitaire"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
                step="0.01"
              />
              <!-- Sélecteur de catégorie -->
              <q-select
                v-model="produit.id_categorie"
                :options="categorieOptions"
                label="Catégorie"
                option-value="id"
                option-label="designation"
                outlined
                dense
                color="primary"
                emit-value
                map-options
                :loading="categorieStore.loading"
                :disable="categorieStore.loading"
                required
              />
              <!-- <q-input
                v-model="produit.id_User"
                label="ID Utilisateur"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
              /> -->

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
            <div class="text-h6">Modifier le Produit</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onUpdate" class="q-gutter-md">
              <q-input
                v-model="editProduitData.nom"
                label="Nom du produit"
                outlined
                dense
                required
                color="primary"
              />
              <q-input
                v-model="editProduitData.description"
                label="Description"
                outlined
                dense
                color="primary"
              />
              <q-input
                v-model="editProduitData.quantite"
                label="Quantité"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
                :input-style="quantiteInputStyle(editProduitData.quantite)"
              />
              <q-input
                v-model="editProduitData.prix_unitaire"
                label="Prix unitaire"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
                step="0.01"
              />
              <!-- Sélecteur de catégorie pour modification -->
              <q-select
                v-model="editProduitData.id_categorie"
                :options="categorieOptions"
                label="Catégorie"
                option-value="id"
                option-label="designation"
                outlined
                dense
                color="primary"
                emit-value
                map-options
                :loading="categorieStore.loading"
                :disable="categorieStore.loading"
                required
              />
              <q-input
                v-model="editProduitData.id_User"
                label="ID Utilisateur"
                type="number"
                outlined
                dense
                color="primary"
                min="0"
              />

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
import { useProduitStore } from 'src/stores/ProduitStore';
import { useCategorieStore } from 'src/stores/CategorieStore';

const $q = useQuasar();
const produitStore = useProduitStore();
const categorieStore = useCategorieStore();

const showAddModal = ref(false);
const showEditModal = ref(false);

// Récupérer l'utilisateur du localStorage
const user = ref(null);
onMounted(() => {
  produitStore.fetchProduits();
  categorieStore.fetchCategories();
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
    produit.value.id_User = user.value.id;
  }
});

const produit = ref({
  nom: '',
  description: '',
  quantite: '',
  prix_unitaire: '',
  id_categorie: '',
  id_User: '', // sera pré-rempli si user existe

});
const editProduitData = ref({
  id: null,
  nom: '',
  description: '',
  quantite: '',
  prix_unitaire: '',
  id_categorie: '',
  id_User: '',
});

// Utilise les produits du store
const produits = computed(() => produitStore.produits);

// Liste des catégories pour le select
const categorieOptions = computed(() => categorieStore.categories);

const columns = [
  { name: 'nom', label: 'Nom', field: 'nom', align: 'left' },
  { name: 'description', label: 'Description', field: 'description', align: 'left' },
  { name: 'quantite', label: 'Quantité', field: 'quantite', align: 'right' },
  { name: 'prix_unitaire', label: 'Prix unitaire', field: 'prix_unitaire', align: 'right', format: val => val + ' $' },
  { name: 'date_creation ', label: 'Date Création', field: 'date_creation', align: 'right' },
  { name: 'actions', label: 'Actions', field: 'actions', align: 'center', sortable: false }
];

// Notification stock insuffisant
const produitsAlert = computed(() =>
  produits.value.filter(p => Number(p.quantite) <= 10)
);
const hasStockAlert = computed(() => produitsAlert.value.length > 0);

function showNotif() {
  if (hasStockAlert.value) {
    $q.notify({
      type: "negative",
      message:
        "Stock insuffisant pour : " +
        produitsAlert.value.map(p => p.nom).join(", "),
      icon: "warning",
      position: "top-right"
    });
  } else {
    $q.notify({
      type: "positive",
      message: "Tous les stocks sont suffisants.",
      icon: "check_circle",
      position: "top-right"
    });
  }
}

// Nouvelle version de onSubmit : enregistrement via l'API
async function onSubmit() {
  if (!produit.value.nom) return;
  try {
    await produitStore.saveProduit({
      nom: produit.value.nom,
      description: produit.value.description,
      quantite: produit.value.quantite,
      prix_unitaire: produit.value.prix_unitaire,
      id_categorie: produit.value.id_categorie,
      id_User: produit.value.id_User,
      date_creation: produit.value.date_creation
    });
    $q.notify({ type: 'positive', message: 'Produit enregistré !' });
    produit.value = { nom: '', description: '', quantite: '', prix_unitaire: '', id_categorie: '', id_User: user.value ? user.value.id : '', date_creation: '' };
    showAddModal.value = false;
  } catch (e) {
    $q.notify({ type: 'negative', message: "Erreur lors de l'enregistrement !" });
  }
}

function deleteProduit(id) {
  produitStore.produits = produitStore.produits.filter(p => p.id !== id);
  $q.notify({ type: 'negative', message: 'Produit supprimé.' });
}

function editProduit(row) {
  editProduitData.value = { ...row };
  showEditModal.value = true;
}

function onUpdate() {
  const idx = produitStore.produits.findIndex(p => p.id === editProduitData.value.id);
  if (idx !== -1) {
    produitStore.produits[idx] = { ...editProduitData.value };
    $q.notify({ type: 'positive', message: 'Produit modifié !' });
  }
  showEditModal.value = false;
}

function quantiteInputStyle(val) {
  return Number(val) <= 10
    ? { color: 'red', fontWeight: 'bold' }
    : {};
}
</script>
<style scoped>
.produit-form-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.produit-form-card {
  max-width: 100%;
  width: 100%;
  margin: auto;
  border-radius: 18px;
  box-shadow: 0 8px 32px 0 rgba(0,0,0,0.10), 0 1.5px 4px 0 rgba(0,0,0,0.08);
}
.produit-title-bar {
  background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
   border-radius: 14px 14px 0 0;
  text-align: left;
  display: flex;
  align-items: center;
}
</style>
