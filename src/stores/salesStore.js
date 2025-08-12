import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { date } from 'quasar'

export const useSalesStore = defineStore('sales', () => {
  const salesData = ref([])
  const loading = ref(false)
  const error = ref(null)
  const authError = ref(false)

  // Formatage des données de vente
  const formatSalesData = (apiData) => {
    return apiData.map(item => ({
      date_vente: item.date_vente,
      produit: item.produit,
      quantite_m3: parseFloat(item.quantite_m3),
      quantite_litre: parseInt(item.quantite_litre),
      prix_usd: parseFloat(item.prix_usd),
      total_usd: parseFloat(item.total_usd),
      total_fc: parseFloat(item.total_fc),
      client: item.client || 'Non spécifié',
      vendeur: item.vendeur || 'Inconnu'
    }))
  }

  // Récupération des données de vente
  const fetchSalesData = async ({ username, password, date }) => {
    loading.value = true
    error.value = null
    authError.value = false

    try {
      const response = await axios.get(
        `http://localhost/Api_Stock/commande/fiche_journaliere_vente/?user=${encodeURIComponent(username)}&mdp=${encodeURIComponent(password)}&date=${date}`,
        {
          validateStatus: function (status) {
            return status >= 200 && status < 500 // Accepter les réponses 401
          }
        }
      )

      if (response.status === 401) {
        authError.value = true
        throw new Error('Authentification échouée. Vérifiez vos identifiants.')
      }

      if (!response.data?.succes) {
        throw new Error(response.data?.message || 'Réponse inattendue de l\'API')
      }

      salesData.value = formatSalesData(response.data.data)
    } catch (err) {
      error.value = err.message || 'Erreur de connexion au serveur'
      console.error('Erreur API:', err)
    } finally {
      loading.value = false
    }
  }

  // Calcul des totaux pour le journal des ventes
  const getSalesTotals = () => {
    return {
      totalQuantityM3: salesData.value.reduce((sum, sale) => sum + sale.quantite_m3, 0),
      totalQuantityLitre: salesData.value.reduce((sum, sale) => sum + sale.quantite_litre, 0),
      totalUSD: salesData.value.reduce((sum, sale) => sum + sale.total_usd, 0),
      totalFC: salesData.value.reduce((sum, sale) => sum + sale.total_fc, 0)
    }
  }

  // Export des données pour Excel
  const exportData = () => {
    return {
      headers: [
        'Date', 'Produit', 'Quantité (m³)', 'Quantité (litres)', 'Prix USD', 'Total USD', 'Total FC', 'Client', 'Vendeur'
      ],
      data: salesData.value.map(sale => ({
        date: date.formatDate(sale.date_vente, 'DD/MM/YYYY HH:mm'),
        produit: sale.produit,
        quantite_m3: sale.quantite_m3.toFixed(3),
        quantite_litre: sale.quantite_litre.toFixed(0),
        prix_usd: sale.prix_usd.toFixed(2),
        total_usd: sale.total_usd.toFixed(2),
        total_fc: sale.total_fc.toFixed(0),
        client: sale.client,
        vendeur: sale.vendeur
      }))
    }
  }

  return {
    salesData,
    loading,
    error,
    authError,
    fetchSalesData,
    getSalesTotals,
    exportData
  }
})
