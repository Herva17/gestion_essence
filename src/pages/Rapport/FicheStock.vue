<template>
  <div class="stock-page">
    <!-- Contenu non imprimable -->
    <div class="no-print">
      <!-- En-tête -->
      <div class="header-section">
        <img :src="logoUrl" alt="Logo" class="logo" />
        <div class="header-info">
          <h1>Station KMJ</h1>
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
        <h2 class="text-h4 q-my-none">FICHE DE STOCK</h2>
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
              <th colspan="4" class="text-center">Entrées</th>
              <th colspan="4" class="text-center">Sorties</th>
              <th colspan="4" class="text-center">Stock</th>
            </tr>
            <tr class="bg-primary text-white">
              <!-- Sous-colonnes Entrées -->
              <th class="text-center">Date</th>
              <th class="text-right">Quantité</th>
              <th class="text-right">P.U ($)</th>
              <th class="text-right">P.T ($)</th>

              <!-- Sous-colonnes Sorties -->
              <th class="text-center">Date</th>
              <th class="text-right">Quantité</th>
              <th class="text-right">P.U ($)</th>
              <th class="text-right">P.T ($)</th>

              <!-- Sous-colonnes Stock -->
              <th class="text-center">Date</th>
              <th class="text-right">Quantité</th>
              <th class="text-right">P.U ($)</th>
              <th class="text-right">P.T ($)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredStockData" :key="item.produit">
              <td class="text-left">{{ item.produit }}</td>

              <!-- Colonne Entrées -->
              <td class="text-center">{{ formatDate(item.date_creation) }}</td>
              <td class="text-right">{{ item.entrees }}</td>
              <td class="text-right">{{ item.prix_usd }}</td>
              <td class="text-right">{{ item.entrees * item.prix_usd }}</td>

              <!-- Colonne Sorties -->
              <td class="text-center">{{ item.derniere_sortie ? formatDate(item.derniere_sortie) : '-' }}</td>
              <td class="text-right">{{ item.sorties }}</td>
              <td class="text-right">{{ item.prix_usd }}</td>
              <td class="text-right">{{ item.sorties * item.prix_usd }}</td>

              <!-- Colonne Stock -->
              <td class="text-center">{{ currentDate }}</td>
              <td class="text-right text-weight-bold">{{ item.stock_actuel }}</td>
              <td class="text-right">{{ item.prix_usd }}</td>
              <td class="text-right">{{ item.stock_actuel * item.prix_usd }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg-grey-3">
              <td colspan="1" class="text-right text-weight-bold">Total Général:</td>
              <!-- Totaux Entrées -->
              <td colspan="3"></td>
              <td class="text-right text-weight-bold">{{ totals.totalEntreesUSD }}</td>

              <!-- Totaux Sorties -->
              <td colspan="3"></td>
              <td class="text-right text-weight-bold">{{ totals.totalSortiesUSD }}</td>

              <!-- Totaux Stock -->
              <td colspan="2"></td>
              <td class="text-right text-weight-bold text-red">{{ totals.totalStock }}</td>
              <td class="text-right text-weight-bold">{{ totals.totalValueUSD }}</td>
            </tr>
          </tfoot>
        </q-markup-table>
      </div>

      <!-- Résumé -->
      <div class="row q-mt-md print-summary">
        <div class="col-md-4 col-sm-12 q-pa-sm">
          <q-card class="bg-blue-1">
            <q-card-section>
              <div class="text-h6 text-center">Total Entrées</div>
              <div class="text-h4 text-center text-blue">{{ totals.totalEntrees }} L</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-4 col-sm-12 q-pa-sm">
          <q-card class="bg-green-1">
            <q-card-section>
              <div class="text-h6 text-center">Total Sorties</div>
              <div class="text-h4 text-center text-green">{{ totals.totalSorties }} L</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-4 col-sm-12 q-pa-sm">
          <q-card class="bg-orange-1">
            <q-card-section>
              <div class="text-h6 text-center">Stock Actuel</div>
              <div class="text-h4 text-center text-orange">{{ totals.totalStock }} L</div>
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
      console.log('Export des données:', stockStore.stockData)
    }

    const formatDate = (dateString) => {
      return dateString ? date.formatDate(dateString, 'DD/MM/YYYY HH:mm') : '-'
    }

    // Filtre les données selon le terme de recherche
    const filteredStockData = computed(() => {
      if (!Array.isArray(stockStore.stockData)) return []
      if (!searchTerm.value) return stockStore.stockData
      return stockStore.stockData.filter(item =>
        item?.produit?.toLowerCase()?.includes(searchTerm.value.toLowerCase()) ?? false
      )
    })

    // Calcul des totaux basés sur les données filtrées
    const totals = computed(() => {
      const data = filteredStockData.value

      const totalEntrees = data.reduce((sum, item) => sum + (item.entrees || 0), 0)
      const totalSorties = data.reduce((sum, item) => sum + (item.sorties || 0), 0)
      const totalStock = data.reduce((sum, item) => sum + (item.stock_actuel || 0), 0)

      const totalEntreesUSD = data.reduce((sum, item) =>
        sum + ((item.entrees || 0) * (item.prix_usd || 0)), 0).toFixed(2)
      const totalSortiesUSD = data.reduce((sum, item) =>
        sum + ((item.sorties || 0) * (item.prix_usd || 0)), 0).toFixed(2)
      const totalValueUSD = data.reduce((sum, item) =>
        sum + ((item.stock_actuel || 0) * (item.prix_usd || 0)), 0).toFixed(2)

      return {
        totalEntrees,
        totalSorties,
        totalStock,
        totalEntreesUSD,
        totalSortiesUSD,
        totalValueUSD,
        // Ajoutez ici d'autres totaux si nécessaire
      }
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
      totals,
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
  max-width: 1400px;
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

/* Styles spécifiques pour l'impression */
@media print {
  body {
    background: white;
    font-size: 12px;
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
    padding: 16px !important;
    margin-bottom: 16px;
  }

  .print-table {
    width: 100% !important;
    font-size: 10px;
  }

  .print-table th {
    background-color: #1976d2 !important;
    color: white !important;
    padding: 6px !important;
  }

  .print-table td {
    padding: 4px !important;
  }

  .print-summary {
    margin-top: 16px !important;
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
