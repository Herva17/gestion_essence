import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { date } from 'quasar'

export const useStockStore = defineStore('stock', () => {
  const stockData = ref([])
  const loading = ref(false)
  const error = ref(null)
  const lastUpdate = ref(null)
  const authError = ref(false)

  // Formatage des données FIFO
  const formatStockData = (apiData) => {
    return apiData.map(item => ({
      id_produit: item.id_produit,
      produit: item.produit,
      id_approvisionnement: item.id_approvisionnement,
      date_entree: item.date_entree,
      date_sortie: item.date_sortie || null,
      prix_unitaire: parseFloat(item.prix_unitaire),
      quantite_entree_m3: parseFloat(item.quantite_entree_m3),
      quantite_entree_litre: parseInt(item.quantite_entree_litre),
      quantite_sortie_m3: parseFloat(item.quantite_sortie_m3),
      quantite_sortie_litre: parseInt(item.quantite_sortie_litre),
      stock_restant_m3: parseFloat(item.stock_restant_m3),
      stock_restant_litre: parseInt(item.stock_restant_litre),
      valeur_entree: parseFloat(item.valeur_entree),
      valeur_sortie: parseFloat(item.valeur_sortie),
      valeur_stock: parseFloat(item.valeur_stock)
    }))
  }

  // Récupération des données FIFO
  const fetchStockData = async (credentials) => {
    loading.value = true
    error.value = null
    authError.value = false

    try {
      const response = await axios.get(
        `http://localhost/Api_Stock/produit/fiche_stock/?user=${encodeURIComponent(credentials.username)}&mdp=${encodeURIComponent(credentials.password)}`,
        {
          validateStatus: function (status) {
            return status >= 200 && status < 500
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

  // Calcul des totaux FIFO
  const totals = () => {
    const data = stockData.value
    return {
      totalEntreeM3: data.reduce((sum, item) => sum + item.quantite_entree_m3, 0).toFixed(3),
      totalEntreeLitre: data.reduce((sum, item) => sum + item.quantite_entree_litre, 0),
      totalSortieM3: data.reduce((sum, item) => sum + item.quantite_sortie_m3, 0).toFixed(3),
      totalSortieLitre: data.reduce((sum, item) => sum + item.quantite_sortie_litre, 0),
      totalStockM3: data.reduce((sum, item) => sum + item.stock_restant_m3, 0).toFixed(3),
      totalStockLitre: data.reduce((sum, item) => sum + item.stock_restant_litre, 0),
      totalValeurUSD: data.reduce((sum, item) => sum + item.valeur_stock, 0).toFixed(2),
      totalValeurFC: data.reduce((sum, item) => sum + (item.valeur_stock * 3000), 0).toFixed(2)
    }
  }

  // Export pour Excel
  const exportData = () => {
    return {
      headers: [
        'ID Produit', 'Produit', 'N° Appro', 'Date Entrée', 'Date Sortie',
        'Quantité Entrée (m³)', 'Quantité Entrée (L)',
        'Quantité Sortie (m³)', 'Quantité Sortie (L)',
        'Stock Restant (m³)', 'Stock Restant (L)',
        'Prix Unitaire ($)', 'Valeur Entrée ($)', 'Valeur Sortie ($)', 'Valeur Stock ($)'
      ],
      data: stockData.value.map(item => ({
        id_produit: item.id_produit,
        produit: item.produit,
        id_approvisionnement: item.id_approvisionnement,
        date_entree: date.formatDate(item.date_entree, 'DD/MM/YYYY'),
        date_sortie: item.date_sortie ? date.formatDate(item.date_sortie, 'DD/MM/YYYY') : '-',
        quantite_entree_m3: item.quantite_entree_m3.toFixed(3),
        quantite_entree_litre: item.quantite_entree_litre.toFixed(0),
        quantite_sortie_m3: item.quantite_sortie_m3.toFixed(3),
        quantite_sortie_litre: item.quantite_sortie_litre.toFixed(0),
        stock_restant_m3: item.stock_restant_m3.toFixed(3),
        stock_restant_litre: item.stock_restant_litre.toFixed(0),
        prix_unitaire: item.prix_unitaire.toFixed(2),
        valeur_entree: item.valeur_entree.toFixed(2),
        valeur_sortie: item.valeur_sortie.toFixed(2),
        valeur_stock: item.valeur_stock.toFixed(2)
      }))
    }
  }

  return {
    stockData,
    loading,
    error,
    authError,
    lastUpdate,
    fetchStockData,
    totals,
    exportData
  }
})
