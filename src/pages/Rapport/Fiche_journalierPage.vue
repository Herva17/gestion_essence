<template>
  <div class="sales-page">
    <!-- Contenu non imprimable -->
    <div class="no-print">
      <!-- En-tête -->
      <div class="header-section">
        <img :src="logoUrl" alt="Logo" class="logo" />
        <div class="header-info">
          <h1>Station KMJ - Journal des Ventes</h1>
          <div class="date-filter">
            <q-input
              v-model="selectedDate"
              filled
              type="date"
              label="Sélectionner la date"
              class="q-mr-sm"
            />
            <q-btn
              color="primary"
              label="Filtrer"
              @click="fetchData"
              :loading="loading"
            />
            <q-btn
              round
              dense
              flat
              icon="refresh"
              @click="fetchData"
              :loading="loading"
              class="q-ml-sm"
            />
          </div>
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
        <h2 class="text-h4 q-my-none">FICHE JOURNALIERE DES VENTES</h2>
        <p class="q-my-none">
          Date: {{ formattedSelectedDate }} | Nombre de transactions: {{ salesData.length }}
        </p>
      </div>

      <!-- Tableau des ventes -->
      <div class="sales-table-section q-mt-md">
        <q-markup-table flat bordered class="print-table">
          <thead>
            <tr class="bg-primary text-white">
              <th class="text-left">N°</th>
              <th class="text-left">Date/Heure</th>
              <th class="text-left">Produit</th>
              <th class="text-right">Quantité (m³)</th>
              <th class="text-right">Quantité (L)</th>
              <th class="text-right">Prix Unitaire ($)</th>
              <th class="text-right">Total ($)</th>
              <th class="text-right">Total (FC)</th>
              <th class="text-left">Client</th>
              <th class="text-left">Vendeur</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sale, index) in salesData" :key="index">
              <td class="text-left">{{ index + 1 }}</td>
              <td class="text-left">{{ formatDateTime(sale.date_vente) }}</td>
              <td class="text-left">{{ sale.produit }}</td>
              <td class="text-right">{{ sale.quantite_m3.toFixed(3) }}</td>
              <td class="text-right">{{ sale.quantite_litre.toFixed(0) }}</td>
              <td class="text-right">{{ sale.prix_usd.toFixed(2) }}</td>
              <td class="text-right">{{ sale.total_usd.toFixed(2) }}</td>
              <td class="text-right">{{ sale.total_fc.toFixed(0) }}</td>
              <td class="text-left">{{ sale.client || '-' }}</td>
              <td class="text-left">{{ sale.vendeur }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg-grey-3">
              <td colspan="3" class="text-right text-weight-bold">Totaux:</td>
              <td class="text-right text-weight-bold">{{ totals.totalQuantityM3.toFixed(3) }}</td>
              <td class="text-right text-weight-bold">{{ totals.totalQuantityLitre.toFixed(0) }}</td>
              <td></td>
              <td class="text-right text-weight-bold">{{ totals.totalUSD.toFixed(2) }}</td>
              <td class="text-right text-weight-bold">{{ totals.totalFC.toFixed(0) }}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </q-markup-table>
      </div>

      <!-- Résumé -->
      <div class="row q-mt-md print-summary">
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-blue-1">
            <q-card-section>
              <div class="text-h6 text-center">Nombre de Ventes</div>
              <div class="text-h4 text-center text-blue">{{ salesData.length }}</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-green-1">
            <q-card-section>
              <div class="text-h6 text-center">Total m³</div>
              <div class="text-h4 text-center text-green">{{ totals.totalQuantityM3.toFixed(3) }}</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-orange-1">
            <q-card-section>
              <div class="text-h6 text-center">Total USD</div>
              <div class="text-h4 text-center text-orange">{{ totals.totalUSD.toFixed(2) }}</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-md-3 col-sm-6 q-pa-sm">
          <q-card class="bg-red-1">
            <q-card-section>
              <div class="text-h6 text-center">Total FC</div>
              <div class="text-h4 text-center text-red">{{ totals.totalFC.toFixed(0) }}</div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <!-- Signature -->
      <div class="signature-section q-mt-xl row justify-between">
        <div class="col-4 text-center">
          <div class="signature-line"></div>
          <p>Le Caissier</p>
        </div>
        <div class="col-4 text-center">
          <div class="signature-line"></div>
          <p>Le Responsable</p>
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
import { useSalesStore } from 'src/stores/salesStore'
import { computed, onMounted, ref } from 'vue'
import { date } from 'quasar'

export default {
  name: 'SalesJournalPage',
  setup() {
    const salesStore = useSalesStore()
    const showAuthDialog = ref(false)
    const authCredentials = ref({
      username: 'herva',
      password: 'mdp'
    })
    const selectedDate = ref(date.formatDate(Date.now(), 'YYYY-MM-DD'))

    const fetchData = () => {
      salesStore.fetchSalesData({
        ...authCredentials.value,
        date: selectedDate.value
      })
    }

    const submitAuth = () => {
      fetchData()
      if (!salesStore.authError) {
        showAuthDialog.value = false
      }
    }

    const printPage = () => {
      window.print()
    }

    const exportExcel = () => {
      salesStore.exportData()
    }

    const formatDateTime = (dateString) => {
      return dateString ? date.formatDate(dateString, 'DD/MM/YYYY HH:mm') : '-'
    }

    onMounted(() => {
      fetchData()
    })

    return {
      logoUrl: "path/to/logo.png",
      selectedDate,
      formattedSelectedDate: computed(() => date.formatDate(selectedDate.value, 'DD/MM/YYYY')),
      salesData: computed(() => salesStore.salesData),
      loading: computed(() => salesStore.loading),
      error: computed(() => salesStore.error),
      authError: computed(() => salesStore.authError),
      totals: computed(() => salesStore.getSalesTotals()),
      authCredentials,
      showAuthDialog,
      fetchData,
      submitAuth,
      printPage,
      exportExcel,
      formatDateTime
    }
  }
}
</script>

<style scoped>
.sales-page {
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

.date-filter {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sales-table-section {
  margin-top: 20px;
  overflow-x: auto;
}

.signature-line {
  width: 200px;
  height: 1px;
  border-bottom: 1px solid #000;
  margin: 0 auto 10px;
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

  .signature-section {
    margin-top: 50px !important;
  }

  /* Masquer les éléments non désirés */
  .q-banner, .q-dialog, .action-buttons {
    display: none !important;
  }

  /* Optimisation pour éviter les coupures de page */
  .sales-table-section {
    page-break-inside: avoid;
  }

  .print-summary, .signature-section {
    page-break-before: avoid;
  }
}
</style>
