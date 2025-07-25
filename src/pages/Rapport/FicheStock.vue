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
              <th class="text-left">Designation</th>
              <th class="text-right">Entrées (L)</th>
              <th class="text-right">Sorties (L)</th>
              <th class="text-right">Stock Actuel</th>
              <th class="text-right">Prix Unitaire ($)</th>
              <th class="text-right">Prix Unitaire (FC)</th>
              <th class="text-right">Valeur ($)</th>
              <th class="text-right">Valeur (FC)</th>
              <th class="text-center">Date Entrée</th>
              <th class="text-center">Date Sortie</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in stockData" :key="item.produit">
              <td class="text-left">{{ item.produit }}</td>
              <td class="text-right">{{ item.entrees }}</td>
              <td class="text-right">{{ item.sorties }}</td>
              <td class="text-right text-weight-bold">{{ item.stock_actuel }}</td>
              <td class="text-right">{{ item.prix_usd }}</td>
              <td class="text-right">{{ item.prix_fc }}</td>
              <td class="text-right">{{ item.total_usd }}</td>
              <td class="text-right">{{ item.total_fc }}</td>
              <td class="text-center">{{ formatDate(item.date_creation) }}</td>
              <td class="text-center">{{ item.derniere_sortie ? formatDate(item.derniere_sortie) : '-' }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg-grey-3">
              <td colspan="3" class="text-right text-weight-bold">Total Général:</td>
              <td class="text-right text-weight-bold text-red">{{ totals.totalStock }}</td>
              <td colspan="2"></td>
              <td class="text-right text-weight-bold">{{ totals.totalValueUSD }}</td>
              <td class="text-right text-weight-bold">{{ totals.totalValueFC }}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </q-markup-table>
      </div>

      <!-- Résumé -->
      <div class="row q-mt-md print-summary">
        <div class="col-md-4 col-sm-12 q-pa-sm">
          <q-card class="bg-blue-1">
            <q-card-section>
              <div class="text-h6 text-center">Total Stock</div>
              <div class="text-h4 text-center text-blue">{{ totals.totalStock }} L</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-4 col-sm-12 q-pa-sm">
          <q-card class="bg-green-1">
            <q-card-section>
              <div class="text-h6 text-center">Valeur Totale ($)</div>
              <div class="text-h4 text-center text-green">{{ totals.totalValueUSD }}</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-4 col-sm-12 q-pa-sm">
          <q-card class="bg-orange-1">
            <q-card-section>
              <div class="text-h6 text-center">Valeur Totale (FC)</div>
              <div class="text-h4 text-center text-orange">{{ totals.totalValueFC }}</div>
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

    onMounted(() => {
      fetchData()
    })

    return {
      logoUrl: "path/to/logo.png",
      currentDate: date.formatDate(Date.now(), 'DD/MM/YYYY'),
      stockData: computed(() => stockStore.stockData),
      loading: computed(() => stockStore.loading),
      error: computed(() => stockStore.error),
      authError: computed(() => stockStore.authError),
      lastUpdate: computed(() => stockStore.lastUpdate),
      totals: computed(() => stockStore.totals()),
      authCredentials,
      showAuthDialog,
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
    font-size: 11px;
  }

  .print-table th {
    background-color: #1976d2 !important;
    color: white !important;
    padding: 8px !important;
  }

  .print-table td {
    padding: 6px !important;
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
