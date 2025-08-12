<template>
  <div class="stock-page">
    <!-- Contenu non imprimable -->
    <div class="no-print">
      <!-- En-tête -->
      <div class="header-section">
        <img :src="logoUrl" alt="Logo" class="logo" />
        <div class="header-info">
          <h1>Station KMJ - Fiche de Stock FIFO</h1>
          <p class="date-info">
            Date: {{ currentDate }} | MAJ: {{ lastUpdate ? formatDate(lastUpdate) : 'N/A' }}
            <q-btn
              round
              dense
              flat
              icon="refresh"
              @click="fetchData"
              :loading="loading"
            />
          </p>
        </div>
      </div>

      <!-- Barre de recherche -->
      <div class="search-bar q-mb-md">
        <q-input
          v-model="searchTerm"
          outlined
          placeholder="Rechercher un produit..."
          clearable
          class="bg-white"
        >
          <template v-slot:prepend>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>

      <!-- Message d'erreur -->
      <q-banner v-if="error" class="bg-negative text-white q-mb-md">
        {{ error }}
        <template v-if="authError">
          <q-btn flat color="white" label="Modifier identifiants" @click="showAuthDialog = true" />
        </template>
        <template v-else>
          <q-btn flat color="white" label="Réessayer" @click="fetchData" />
        </template>
      </q-banner>

      <!-- Dialogue d'authentification -->
      <q-dialog v-model="showAuthDialog" persistent>
        <q-card style="width: 400px">
          <q-card-section class="row items-center">
            <q-avatar icon="lock" color="primary" text-color="white" />
            <span class="q-ml-sm text-h6">Authentification API</span>
          </q-card-section>

          <q-card-section>
            <q-form @submit.prevent="submitAuth">
              <q-input
                v-model="authCredentials.username"
                label="Utilisateur"
                filled
                lazy-rules
                :rules="[val => !!val || 'Champ obligatoire']"
              />

              <q-input
                v-model="authCredentials.password"
                label="Mot de passe"
                filled
                type="password"
                lazy-rules
                :rules="[val => !!val || 'Champ obligatoire']"
                class="q-mt-sm"
              />
            </q-form>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Annuler" color="grey" v-close-popup />
            <q-btn
              label="Valider"
              color="primary"
              @click="submitAuth"
              :loading="loading"
            />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>

    <!-- Zone à imprimer -->
     <div class="print-area">
      <div class="print-header bg-primary text-white q-pa-md text-center">
        <h2 class="text-h4 q-my-none">FICHE DE STOCK FIFO</h2>
        <p class="q-my-none">
          Date: {{ currentDate }} | Dernière mise à jour: {{ lastUpdate ? formatDate(lastUpdate) : 'N/A' }}
        </p>
      </div>

      <!-- Tableau des stocks -->
      <div class="stock-table-section q-mt-md">
        <q-markup-table flat bordered class="print-table">
          <thead>
            <tr class="bg-primary text-white">
              <th rowspan="2" class="text-left">Produit</th>
              <th colspan="5" class="text-center">ENTREE</th>
              <th colspan="5" class="text-center">SORTIE</th>
              <th colspan="5" class="text-center">STOCK</th>
            </tr>
            <tr class="bg-primary text-white">
              <!-- Sous-colonnes Entrée -->
              <th class="text-center">Date</th>
              <th class="text-right">Qte (m³)</th>
              <th class="text-right">Qte (L)</th>
              <th class="text-right">P.U ($)</th>
              <th class="text-right">P.T ($)</th>

              <!-- Sous-colonnes Sortie -->
              <th class="text-center">Date</th>
              <th class="text-right">Qte (m³)</th>
              <th class="text-right">Qte (L)</th>
              <th class="text-right">P.U ($)</th>
              <th class="text-right">P.T ($)</th>

              <!-- Sous-colonnes Stock -->
              <th class="text-center">Date</th>
              <th class="text-right">Qte (m³)</th>
              <th class="text-right">Qte (L)</th>
              <th class="text-right">P.U ($)</th>
              <th class="text-right">P.T ($)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in filteredStockData" :key="index">
              <td class="text-left">{{ item.produit }}</td>

              <!-- Colonne Entrée -->
              <td class="text-center">{{ formatDate(item.date_entree) }}</td>
              <td class="text-right">{{ item.quantite_entree_m3.toFixed(3) }}</td>
              <td class="text-right">{{ item.quantite_entree_litre.toFixed(0) }}</td>
              <td class="text-right">{{ item.prix_unitaire.toFixed(2) }}</td>
              <td class="text-right">{{ (item.quantite_entree_m3 * item.prix_unitaire).toFixed(2) }}</td>

              <!-- Colonne Sortie -->
              <td class="text-center">{{ item.date_sortie ? formatDate(item.date_sortie) : '-' }}</td>
              <td class="text-right">{{ item.quantite_sortie_m3.toFixed(3) }}</td>
              <td class="text-right">{{ item.quantite_sortie_litre.toFixed(0) }}</td>
              <td class="text-right">{{ item.prix_unitaire.toFixed(2) }}</td>
              <td class="text-right">{{ (item.quantite_sortie_m3 * item.prix_unitaire).toFixed(2) }}</td>

              <!-- Colonne Stock -->
              <td class="text-center">{{ currentDate }}</td>
              <td class="text-right text-weight-bold">{{ item.stock_restant_m3.toFixed(3) }}</td>
              <td class="text-right text-weight-bold">{{ item.stock_restant_litre.toFixed(0) }}</td>
              <td class="text-right">{{ item.prix_unitaire.toFixed(2) }}</td>
              <td class="text-right text-weight-bold">{{ item.valeur_stock.toFixed(2) }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg-grey-3">
              <td class="text-right text-weight-bold">Totaux:</td>

              <!-- Totaux Entrée -->
              <td></td>
              <td class="text-right">{{ totals.totalEntreeM3 }}</td>
              <td class="text-right">{{ totals.totalEntreeLitre }}</td>
              <td></td>
              <td class="text-right">{{ totals.totalEntreesUSD }}</td>

              <!-- Totaux Sortie -->
              <td></td>
              <td class="text-right">{{ totals.totalSortieM3 }}</td>
              <td class="text-right">{{ totals.totalSortieLitre }}</td>
              <td></td>
              <td class="text-right">{{ totals.totalSortiesUSD }}</td>

              <!-- Totaux Stock -->
              <td></td>
              <td class="text-right text-weight-bold text-red">{{ totals.totalStockM3 }}</td>
              <td class="text-right text-weight-bold text-red">{{ totals.totalStockLitre }}</td>
              <td></td>
              <td class="text-right text-weight-bold">{{ totals.totalValeurUSD }}</td>
            </tr>
          </tfoot>
        </q-markup-table>
      </div>

      <!-- Résumé -->
      <div class="row q-mt-md print-summary">
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-blue-1">
            <q-card-section>
              <div class="text-h6 text-center">Total Entrées</div>
              <div class="text-h4 text-center text-blue">{{ totals.totalEntreeM3 }} m³</div>
              <div class="text-subtitle2 text-center">{{ totals.totalEntreeLitre }} L</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-green-1">
            <q-card-section>
              <div class="text-h6 text-center">Total Sorties</div>
              <div class="text-h4 text-center text-green">{{ totals.totalSortieM3 }} m³</div>
              <div class="text-subtitle2 text-center">{{ totals.totalSortieLitre }} L</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-orange-1">
            <q-card-section>
              <div class="text-h6 text-center">Stock Actuel</div>
              <div class="text-h4 text-center text-orange">{{ totals.totalStockM3 }} m³</div>
              <div class="text-subtitle2 text-center">{{ totals.totalStockLitre }} L</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-red-1">
            <q-card-section>
              <div class="text-h6 text-center">Valeur Stock</div>
              <div class="text-h4 text-center text-red">{{ totals.totalValeurUSD }} $</div>
              <div class="text-subtitle2 text-center">{{ (totals.totalValeurUSD * 3000).toFixed(0) }} FC</div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>

    <!-- Actions (non imprimables) -->
    <div class="no-print row justify-end q-mt-md">
      <q-btn
        color="primary"
        icon="print"
        label="Imprimer"
        @click="printPage"
        class="q-mr-sm"
      />
      <q-btn
        color="positive"
        icon="file_download"
        label="Excel"
        @click="exportExcel"
      />
    </div>
  </div>
</template>

<script>
import { useStockStore } from 'src/stores/stockStore'
import { computed, onMounted, ref } from 'vue'
import { date } from 'quasar'

export default {
  name: 'StockPage',
  setup() {
    const stockStore = useStockStore()
    const showAuthDialog = ref(false)
    const authCredentials = ref({
      username: 'herva',
      password: 'mdp'
    })
    const searchTerm = ref('')

    const fetchData = () => {
      stockStore.fetchStockData(authCredentials.value)
    }

    const submitAuth = () => {
      fetchData()
      if (!stockStore.authError) {
        showAuthDialog.value = false
      }
    }

    const printPage = () => {
      window.print()
    }

    const exportExcel = () => {
      stockStore.exportData()
    }

    const formatDate = (dateString) => {
      return dateString ? date.formatDate(dateString, 'DD/MM/YYYY') : '-'
    }

    // Filtre les données selon le terme de recherche
    const filteredStockData = computed(() => {
      if (!Array.isArray(stockStore.stockData)) return []
      if (!searchTerm.value) return stockStore.stockData
      return stockStore.stockData.filter(item =>
        item?.produit?.toLowerCase()?.includes(searchTerm.value.toLowerCase()) ?? false
      )
    })

    onMounted(() => {
      fetchData()
    })

    return {
      logoUrl: "path/to/logo.png",
      currentDate: date.formatDate(Date.now(), 'DD/MM/YYYY'),
      stockData: computed(() => stockStore.stockData),
      filteredStockData,
      loading: computed(() => stockStore.loading),
      error: computed(() => stockStore.error),
      authError: computed(() => stockStore.authError),
      lastUpdate: computed(() => stockStore.lastUpdate),
      totals: computed(() => stockStore.totals()),
      authCredentials,
      showAuthDialog,
      searchTerm,
      fetchData,
      submitAuth,
      printPage,
      exportExcel,
      formatDate
    }
  }
}
</script>

<style scoped>
.stock-page {
  max-width: 1800px;
  margin: 0 auto;
  padding: 20px;
}

.header-section {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.logo {
  width: 100px;
  height: auto;
  margin-right: 20px;
}

.date-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-bar {
  max-width: 400px;
  margin-bottom: 20px;
}

.stock-table-section {
  margin-top: 20px;
  overflow-x: auto;
}

.print-table {
  font-size: 12px;
}

.print-table th {
  white-space: nowrap;
}

/* Styles spécifiques pour l'impression */
@media print {
  body {
    background: white;
    font-size: 10px;
    padding: 0;
    margin: 0;
  }

  .no-print {
    display: none !important;
  }

  .print-area {
    width: 100%;
    margin: 0;
    padding: 0;
  }

  .print-header {
    background-color: #1976d2 !important;
    color: white !important;
    padding: 10px !important;
    margin-bottom: 10px;
  }

  .print-table {
    width: 100% !important;
    font-size: 8px;
  }

  .print-table th {
    background-color: #1976d2 !important;
    color: white !important;
    padding: 4px !important;
  }

  .print-table td {
    padding: 3px !important;
  }

  .print-summary {
    margin-top: 10px !important;
  }

  /* Masquer les éléments non désirés */
  .q-banner, .q-dialog, .action-buttons {
    display: none !important;
  }

  /* Optimisation pour éviter les coupures de page */
  .stock-table-section {
    page-break-inside: avoid;
  }

  .print-summary {
    page-break-before: avoid;
  }
}
</style>
