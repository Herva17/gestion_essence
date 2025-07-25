import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useStockStore = defineStore('stock', () => {
  const stockData = ref([])
  const loading = ref(false)
  const error = ref(null)
  const lastUpdate = ref(null)
  const authError = ref(false)

  // Formatage des données
  const formatStockData = (apiData) => {
    return apiData.map(item => ({
      produit: item.produit,
      entrees: item.entrees,
      sorties: item.sorties,
      stock_actuel: item.stock_actuel,
      prix_usd: parseFloat(item.prix_usd).toFixed(2),
      prix_fc: parseFloat(item.prix_fc).toFixed(2),
      total_usd: parseFloat(item.total_usd).toFixed(2),
      total_fc: parseFloat(item.total_fc).toFixed(2),
      date_creation: item.date_creation,
      derniere_sortie: item.derniere_sortie === 'NULL' ? null : item.derniere_sortie
    }))
  }

  // Récupération des données
  const fetchStockData = async (credentials) => {
    loading.value = true
    error.value = null
    authError.value = false

    try {
      const response = await axios.get(
        `http://localhost/Api_Stock/produit/fiche_stock/?user=${encodeURIComponent(credentials.username)}&mdp=${encodeURIComponent(credentials.password)}`,
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

      stockData.value = formatStockData(response.data.data)
      lastUpdate.value = new Date()
    } catch (err) {
      error.value = err.message || 'Erreur de connexion au serveur'
      console.error('Erreur API:', err)
    } finally {
      loading.value = false
    }
  }

  // Calcul des totaux
  const totals = () => {
    return {
      totalStock: stockData.value.reduce((sum, item) => sum + item.stock_actuel, 0),
      totalValueUSD: stockData.value.reduce((sum, item) => sum + parseFloat(item.total_usd), 0).toFixed(2),
      totalValueFC: stockData.value.reduce((sum, item) => sum + parseFloat(item.total_fc), 0).toFixed(2)
    }
  }

  return {
    stockData,
    loading,
    error,
    authError,
    lastUpdate,
    fetchStockData,
    totals
  }
})
