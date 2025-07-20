<template>
  <div class="q-pa-md commande-form-bg">
    <q-card class="q-pa-lg commande-form-card">
      <!-- Bandeau titre -->
      <div
        class="commande-title-bar q-pa-md q-mb-md row items-center justify-between"
      >
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="shopping_cart" class="q-mr-sm" />
          Gestion des Commandes
        </div>
        <div class="text-subtitle1 text-white">
          Total: {{ totalCommandes }} commande(s)
        </div>
      </div>

      <!-- Section des boutons d'action -->
      <q-card-section>
        <div class="row items-center justify-between q-mb-md">
          <div class="text-h5 text-primary text-weight-bold">
            Liste des Commandes
          </div>
          <div class="flex gap-2">
            <q-btn
              color="primary"
              icon="refresh"
              label="Actualiser"
              @click="fetchCommandes"
              class="q-mr-sm"
              :loading="loading"
            />
            <q-btn
              color="primary"
              icon="add"
              label="Ajouter Commande"
              @click="openAddModal"
              :loading="loading"
            />
          </div>
        </div>

        <!-- Tableau des commandes -->
        <q-table
          :rows="commandes"
          :columns="columns"
          row-key="id"
          flat
          bordered
          :loading="loading"
          :pagination="{ rowsPerPage: 5 }"
          no-data-label="Aucune commande enregistrée"
        >
          <template #body-cell-client="props">
            <q-td :props="props">
              {{ getClientName(props.row.id_client) }}
            </q-td>
          </template>

          <template #body-cell-produit="props">
            <q-td :props="props">
              {{ getProduitName(props.row.id_produit) }}
            </q-td>
          </template>

          <template #body-cell-actions="props">
            <q-td align="center">
              <div class="flex gap-2">
                <q-btn
                  dense
                  flat
                  round
                  icon="visibility"
                  color="info"
                  @click="viewCommande(props.row)"
                  :disable="loading"
                />
                <q-btn
                  dense
                  flat
                  round
                  icon="edit"
                  color="primary"
                  @click="editCommande(props.row)"
                  :disable="loading"
                />
                <q-btn
                  dense
                  flat
                  round
                  icon="delete"
                  color="negative"
                  @click="confirmDeleteCommande(props.row.id)"
                  :disable="loading"
                />
              </div>
            </q-td>
          </template>
        </q-table>

        <!-- Modal d'ajout de commande -->
        <q-dialog v-model="showAddModal" persistent>
    <q-card style="min-width: 400px; max-width: 90vw;">
      <q-card-section>
        <div class="text-h6">Ajouter une Commande</div>
      </q-card-section>
      <q-card-section>
        <q-form @submit.prevent="addCommande">
          <!-- Champ ID User (lecture seule) -->
          <q-input
            v-model="newCommande.id_User"
            label="ID Utilisateur (automatique)"
            dense
            outlined
            readonly
            class="q-mb-md"
            hint="ID récupéré depuis la session"
          />

          <q-select
            v-model="newCommande.id_client"
            :options="clientOptions"
            option-label="label"
            option-value="id"
            emit-value
            map-options
            label="Client"
            dense
            outlined
            required
            class="q-mb-md"
          />
          <q-select
            v-model="newCommande.id_produit"
            :options="produitOptions"
            option-label="nom"
            option-value="id"
            emit-value
            map-options
            label="Produit"
            dense
            outlined
            required
            class="q-mb-md"
            @update:model-value="checkStock"
          />
          <q-input
            v-model.number="newCommande.quantite"
            type="number"
            label="Quantité"
            min="1"
            dense
            outlined
            required
            class="q-mb-md"
            @update:model-value="checkStock"
          />
          <div class="row justify-end q-gutter-sm">
            <q-btn label="Annuler" color="negative" flat v-close-popup />
            <q-btn label="Ajouter" color="primary" type="submit" :loading="loading" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>

        <!-- Modal de modification de commande -->
        <q-dialog v-model="showEditModal" persistent>
          <q-card style="min-width: 400px; max-width: 90vw">
            <q-card-section>
              <div class="text-h6">Modifier la Commande</div>
            </q-card-section>
            <q-card-section>
              <q-form @submit.prevent="updateCommande">
                <q-select
                  v-model="newCommande.id_client"
                  :options="clientOptions"
                  option-label="label"
                  option-value="id"
                  emit-value
                  map-options
                  label="Client"
                  dense
                  outlined
                  required
                  class="q-mb-md"
                />
                <q-select
                  v-model="newCommande.id_produit"
                  :options="produitOptions"
                  option-label="nom"
                  option-value="id"
                  emit-value
                  map-options
                  label="Produit"
                  dense
                  outlined
                  required
                  class="q-mb-md"
                />
                <q-input
                  v-model.number="selectedCommande.quantite"
                  type="number"
                  label="Quantité"
                  min="1"
                  dense
                  outlined
                  required
                  class="q-mb-md"
                />
                <div class="row justify-end q-gutter-sm">
                  <q-btn label="Annuler" color="negative" flat v-close-popup />
                  <q-btn
                    label="Modifier"
                    color="primary"
                    type="submit"
                    :loading="loading"
                  />
                </div>
              </q-form>
            </q-card-section>
          </q-card>
        </q-dialog>

        <!-- Dialog de facture -->
        <q-dialog v-model="showViewModal" persistent>
          <q-card style="min-width: 70vw">
            <q-card-section class="row items-center q-pb-none">
              <div class="text-h6">Facture #{{ selectedCommande.id }}</div>
              <q-space />
              <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>

            <!-- Contenu de la facture -->
            <q-card-section>
              <div class="facture-page">
                <!-- Section du logo -->
                <div class="logo-section text-center mb-4">
                  <q-img
                    :src="
                      logoUrl ||
                      'https://via.placeholder.com/150x80?text=KMJ+Logo'
                    "
                    style="max-width: 150px; height: auto"
                  />
                  <div class="text-h6 text-weight-bold q-mt-sm">
                    STATION KMJ
                  </div>
                </div>

                <!-- Titre de la facture -->
                <h1 class="facture-title text-center mb-8">FACTURE CLIENT</h1>

                <!-- Informations du client -->
                <div class="client-info q-mb-md">
                  <div class="text-subtitle1 text-weight-bold">
                    INFORMATIONS CLIENT
                  </div>
                  <q-separator class="my-2" />
                  <div class="row q-col-gutter-md q-mt-sm">
                    <div class="col-6">
                      <p>
                        <strong>Nom:</strong>
                        {{ getClientFullName(selectedCommande.id_client) }}
                      </p>
                      <p>
                        <strong>Téléphone:</strong>
                        {{ getClientPhone(selectedCommande.id_client) }}
                      </p>
                    </div>
                    <div class="col-6">
                      <p>
                        <strong>Date:</strong>
                        {{ formatDate(selectedCommande.date_commande) }}
                      </p>
                      <p>
                        <strong>N° Commande:</strong> {{ selectedCommande.id }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Détails de la facture -->
                <div class="facture-details">
                  <div class="text-subtitle1 text-weight-bold">
                    DÉTAILS DE LA FACTURE
                  </div>
                  <q-separator class="my-2" />
                  <q-table
                    flat
                    bordered
                    :rows="factureItems"
                    :columns="factureColumns"
                    row-key="id"
                    hide-pagination
                    hide-bottom
                    class="q-mt-md"
                  >
                    <template #body-cell-unitPrice="props">
                      <q-td :props="props">
                        {{ props.row.unitPrice }} $
                        <br />
                        {{
                          (props.row.unitPrice * exchangeRate).toLocaleString()
                        }}
                        FC
                      </q-td>
                    </template>

                    <template #body-cell-total="props">
                      <q-td :props="props">
                        {{ props.row.total }} $
                        <br />
                        {{
                          (props.row.total * exchangeRate).toLocaleString()
                        }}
                        FC
                      </q-td>
                    </template>
                  </q-table>

                  <!-- Total de la facture -->
                  <div class="facture-total text-right q-mt-md">
                    <div class="text-h6">
                      <strong>TOTAL:</strong>
                      {{ totalGeneral }} $ /
                      {{ (totalGeneral * exchangeRate).toLocaleString() }} FC
                    </div>
                    <div class="text-caption">
                      Taux: 1$ = {{ exchangeRate.toLocaleString() }} FC
                    </div>
                  </div>
                </div>

                <!-- Boutons d'action -->
                <div
                  class="print-button-section q-mt-lg flex justify-center gap-2"
                >
                  <q-btn
                    color="primary"
                    icon="print"
                    label="Imprimer"
                    @click="printFacture"
                    class="q-mr-sm"
                  />
                  <q-btn
                    color="secondary"
                    icon="cloud_upload"
                    label="Changer Logo"
                    @click="triggerLogoUpload"
                  />
                  <input
                    ref="logoInput"
                    type="file"
                    accept="image/*"
                    style="display: none"
                    @change="handleLogoUpload"
                  />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </q-dialog>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useQuasar } from "quasar";
import { useCommandeStore } from "stores/CommandeStore";
import { useClientStore } from "stores/ClientStore";
import { useProduitStore } from "stores/ProduitStore";

const $q = useQuasar();
const commandeStore = useCommandeStore();
const clientStore = useClientStore();
const produitStore = useProduitStore();

// Récupérer l'ID de l'utilisateur connecté depuis le localStorage
const getCurrentUserId = () => {
  const user = JSON.parse(localStorage.getItem("user"));
  return user?.id || 1; // Valeur par défaut si non trouvé
};

// États réactifs
const newCommande = ref({
  id_client: null,
  id_produit: null,
  id_User: getCurrentUserId(), // Utilisation de l'ID utilisateur connecté
  quantite: 1,
});

const selectedCommande = ref({
  id: null,
  id_client: null,
  id_produit: null,
  id_User: getCurrentUserId(),
  quantite: 1,
  date_commande: "",
});

const showAddModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const logoUrl = ref(null);
const logoInput = ref(null);
const exchangeRate = ref(3000);
const isLoading = ref(false);

const columns = [
  { name: "id", label: "ID", field: "id", align: "left", sortable: true },
  { name: "client", label: "Client", field: "id_client", align: "left" },
  { name: "produit", label: "Produit", field: "id_produit", align: "left" },
  {
    name: "quantite",
    label: "Quantité",
    field: "quantite",
    align: "center",
    sortable: true,
  },
   {
    name: 'prix_unitaire',
    label: 'Prix Unitaire',
    field: row => getProduitQuantite(row.id_produit),
    align: 'left'
  },
  {
    name: "date_commande",
    label: "Date Commande",
    field: "date_commande",
    align: "center",
    sortable: true,
  },
  { name: "actions", label: "Actions", align: "center" },
];

const factureColumns = [
  { name: "name", label: "ARTICLE", field: "name", align: "left" },
  { name: "quantity", label: "QUANTITÉ", field: "quantity", align: "center" },
  {
    name: "unitPrice",
    label: "PRIX UNITAIRE",
    field: "unitPrice",
    align: "right",
  },
  { name: "total", label: "TOTAL", field: "total", align: "right" },
];

const commandes = computed(() => commandeStore.commandes || []);
const totalCommandes = computed(() => commandeStore.totalCommandes || 0);
const loading = computed(() => commandeStore.loading || isLoading.value);

const factureItems = computed(() => {
  if (!selectedCommande.value?.id_produit) return [];
  const produit = (produitStore.produits || []).find(
    (p) => p.id === selectedCommande.value.id_produit
  );
  if (!produit) return [];
  return [
    {
      id: 1,
      name: produit.nom || "Produit inconnu",
      quantity: selectedCommande.value.quantite || 0,
      unitPrice: Number(produit.prix_unitaire || 0),
      total:
        Number(produit.prix_unitaire || 0) *
        Number(selectedCommande.value.quantite || 0),
    },
  ];
});

function getProduitQuantite(id) {
  if (!id || !produitStore.produits) return "Non catégorisé";
  const produit = produitStore.produits.find(c => c.id === id);
  return produit?.prix_unitaire || "Non catégorisé";
}

const totalGeneral = computed(() => {
  return factureItems.value.reduce(
    (sum, item) => sum + Number(item.total || 0),
    0
  );
});

const clientOptions = computed(() => {
  return (clientStore.clients || [])
    .map((client) => ({
      id: client.id,
      nom: client.nom,
      prenom: client.prenom,
      telephone: client.telephone,
      email: client.email,
      label: `${client.prenom || ""} ${client.nom || ""} (${
        client.telephone || ""
      })`.trim(),
    }))
    .filter((c) => c.label !== "()");
});

const produitOptions = computed(() => produitStore.produits || []);

function getClientName(id) {
  if (!id || !clientStore.clients) return "Client inconnu";
  const client = clientStore.clients.find((c) => c.id === id);
  return client
    ? `${client.prenom || ""} ${client.nom || ""} (${
        client.telephone || ""
      })`.trim()
    : "Client inconnu";
}

function getClientFullName(id) {
  if (!id || !clientStore.clients) return "Client inconnu";
  const client = clientStore.clients.find((c) => c.id === id);
  return client
    ? `${client.prenom || ""} ${client.nom || ""}`.trim()
    : "Client inconnu";
}

function getClientPhone(id) {
  if (!id || !clientStore.clients) return "Non disponible";
  const client = clientStore.clients.find((c) => c.id === id);
  return client?.telephone || "Non disponible";
}

function getClientEmail(id) {
  if (!id || !clientStore.clients) return "Non disponible";
  const client = clientStore.clients.find((c) => c.id === id);
  return client?.email || "Non disponible";
}

function getProduitName(id) {
  if (!id || !produitStore.produits) return "Inconnu";
  const produit = produitStore.produits.find((p) => p.id === id);
  return produit?.nom || "Inconnu";
}

function formatDate(dateString) {
  if (!dateString) return "Date inconnue";
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString("fr-FR");
  } catch {
    return dateString;
  }
}

async function fetchCommandes() {
  try {
    isLoading.value = true;
    await Promise.all([
      commandeStore.fetchCommandes(),
      commandeStore.fetchTotalCommandes(),
      clientStore.fetchClients(),
      produitStore.fetchProduits(),
    ]);
  } catch (error) {
    $q.notify({
      type: "negative",
      message: "Erreur lors du chargement des données",
      caption: error.message,
    });
  } finally {
    isLoading.value = false;
  }
}

function viewCommande(commande) {
  selectedCommande.value = { ...commande };
  showViewModal.value = true;
}

function triggerLogoUpload() {
  logoInput.value?.click();
}

function handleLogoUpload(event) {
  const file = event.target.files?.[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      logoUrl.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function printFacture() {
  window.print();
}

function openAddModal() {
  resetNewCommande();
  showAddModal.value = true;
}

// Fonction pour vérifier le stock en temps réel
function checkStock() {
  if (!newCommande.value.id_produit || !newCommande.value.quantite) return;

  const produit = produitStore.produits.find(p => p.id === newCommande.value.id_produit);

  if (!produit) {
    console.warn("Produit non trouvé");
    return;
  }

  console.log("Vérification stock:", {
    quantite: newCommande.value.quantite,
    stock: produit.stock
  });

  if (newCommande.value.quantite > produit.stock) {
    $q.notify({
      type: "negative",
      message: `Stock insuffisant! Disponible: ${produit.stock}, Demandé: ${newCommande.value.quantite}`,
      position: "top",
      timeout: 5000,
      actions: [{ icon: "warning", color: "white" }]
    });
    return true; // Retourne true si stock insuffisant
  }
  return false; // Retourne false si stock suffisant
}

async function addCommande() {
  if (
    !newCommande.value.id_client ||
    !newCommande.value.id_produit ||
    !newCommande.value.quantite
  ) {
    $q.notify({
      type: "negative",
      message: "Les champs Client, Produit et Quantité sont obligatoires"
    });
    return;
  }

  try {
    isLoading.value = true;
    const response = await commandeStore.saveCommande({
      id_client: Number(newCommande.value.id_client),
      id_produit: Number(newCommande.value.id_produit),
      id_User: Number(newCommande.value.id_User),
      quantite: Number(newCommande.value.quantite)
    });

    // Gestion de la réponse de l'API
    if (response && response.success === false) {
      $q.notify({
        type: "negative",
        message: response.message || "Erreur lors de l'ajout de la commande",
        position: "top",
        timeout: 5000
      });
      return;
    }

    $q.notify({
      type: "positive",
      message: "Commande ajoutée avec succès"
    });
    resetNewCommande();
    showAddModal.value = false;
    await fetchCommandes();
  } catch (error) {
    console.error("Erreur API:", error.response?.data); // Log pour débogage
    $q.notify({
      type: "negative",
      message: error.response?.data?.message || "Erreur lors de l'ajout de la commande",
      caption: error.message
    });
  } finally {
    isLoading.value = false;
  }
}

function editCommande(commande) {
  selectedCommande.value = { ...commande };
  showEditModal.value = true;
}

async function updateCommande() {
  try {
    isLoading.value = true;
    await commandeStore.updateCommande({
      id: selectedCommande.value.id,
      id_client: selectedCommande.value.id_client,
      id_produit: selectedCommande.value.id_produit,
      id_User: selectedCommande.value.id_User,
      quantite: selectedCommande.value.quantite,
    });
    $q.notify({
      type: "positive",
      message: "Commande mise à jour avec succès",
    });
    showEditModal.value = false;
    await fetchCommandes();
  } catch (error) {
    $q.notify({
      type: "negative",
      message:
        error.response?.data?.message ||
        "Erreur lors de la mise à jour de la commande",
      caption: error.message,
    });
  } finally {
    isLoading.value = false;
  }
}

function confirmDeleteCommande(id) {
  $q.dialog({
    title: "Confirmation",
    message: "Voulez-vous vraiment supprimer cette commande ?",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await deleteCommande(id);
  });
}

async function deleteCommande(id) {
  try {
    isLoading.value = true;
    await commandeStore.deleteCommande(id);
    $q.notify({
      type: "positive",
      message: "Commande supprimée avec succès",
    });
    await fetchCommandes();
  } catch (error) {
    $q.notify({
      type: "negative",
      message:
        error.response?.data?.message ||
        "Erreur lors de la suppression de la commande",
      caption: error.message,
    });
  } finally {
    isLoading.value = false;
  }
}

function resetNewCommande() {
  newCommande.value = {
    id_client: null,
    id_produit: null,
    id_User: getCurrentUserId(),
    quantite: 1,
  };
}

onMounted(() => {
  fetchCommandes();
});

defineExpose({
  commandes,
  totalCommandes,
  loading,
  clientStore,
  produitStore,
  newCommande,
  selectedCommande,
  showAddModal,
  showEditModal,
  showViewModal,
  logoUrl,
  logoInput,
  exchangeRate,
  columns,
  factureColumns,
  factureItems,
  totalGeneral,
  clientOptions,
  produitOptions,
  getClientName,
  getClientFullName,
  getClientPhone,
  getClientEmail,
  getProduitName,
  formatDate,
  viewCommande,
  triggerLogoUpload,
  handleLogoUpload,
  printFacture,
  openAddModal,
  addCommande,
  editCommande,
  updateCommande,
  deleteCommande,
  confirmDeleteCommande,
  fetchCommandes,
  resetNewCommande,
});
</script>

<style scoped>
.commande-form-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.commande-form-card {
  max-width: 100%;
  width: 100%;
  margin: auto;
  border-radius: 18px;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1), 0 1.5px 4px 0 rgba(0, 0, 0, 0.08);
}

.commande-title-bar {
  background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
  border-radius: 14px 14px 0 0;
  text-align: left;
  display: flex;
  align-items: center;
}

/* Styles pour la facture */
.facture-page {
  font-family: Arial, sans-serif;
  padding: 20px;
  background-color: white;
}

.client-info,
.facture-details {
  margin-bottom: 20px;
}

.facture-total {
  margin-top: 20px;
  padding-top: 10px;
  border-top: 2px solid #eee;
}

.print-button-section {
  text-align: center;
  margin-top: 30px;
}

/* Styles d'impression */
@media print {
  .print-button-section {
    display: none;
  }

  body {
    background: white;
    font-size: 12pt;
  }

  .facture-page {
    padding: 0;
    border: none;
  }

  .q-card {
    box-shadow: none;
  }
}
</style>
