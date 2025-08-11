<template>
  <div class="container-fluid">
    <div class="row mb-4">
      <div class="col-md-12">
        <h2 class="text-center">Gestion des Ventes</h2>
      </div>
    </div>

    <!-- Formulaire d'ajout de vente -->
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5>Enregistrer une nouvelle vente</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitVente">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="id_commande">Commande</label>
                    <select
                      v-model="newVente.id_commande"
                      class="form-control"
                      required
                      @change="loadCommandeInfo"
                    >
                      <option value="">Sélectionner une commande</option>
                      <option
                        v-for="commande in commandesDisponibles"
                        :key="commande.id"
                        :value="commande.id"
                      >
                        #{{ commande.id }} - {{ commande.produit }} ({{
                          commande.quantite
                        }}
                        restants)
                      </option>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="quantite">Quantité</label>
                    <input
                      type="number"
                      v-model.number="newVente.quantite"
                      class="form-control"
                      min="1"
                      :max="quantiteMax"
                      required
                    />
                    <small class="text-muted"
                      >Quantité disponible: {{ quantiteMax }}</small
                    >
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="date_vente">Date de vente</label>
                    <input
                      type="date"
                      v-model="newVente.date_vente"
                      class="form-control"
                      required
                    />
                  </div>
                </div>
              </div>

              <div class="row mt-2" v-if="selectedCommande">
                <div class="col-md-4">
                  <p><strong>Client:</strong> {{ selectedCommande.client }}</p>
                </div>
                <div class="col-md-4">
                  <p>
                    <strong>Produit:</strong> {{ selectedCommande.produit }}
                  </p>
                </div>
                <div class="col-md-4">
                  <p>
                    <strong>Prix unitaire:</strong>
                    {{ selectedCommande.prix_unitaire }} $
                  </p>
                </div>
              </div>

              <div class="text-center mt-3">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="loading"
                >
                  <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm"
                  ></span>
                  Enregistrer
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des ventes -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header bg-secondary text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h5>Liste des ventes</h5>
              <div>
                <button class="btn btn-sm btn-light" @click="fetchVentes">
                  <i class="fas fa-sync-alt"></i> Actualiser
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center">
              <div class="spinner-border text-primary"></div>
              <p>Chargement en cours...</p>
            </div>

            <div v-if="error" class="alert alert-danger">
              {{ error }}
            </div>

            <div
              v-if="!loading && ventes.length === 0"
              class="alert alert-info"
            >
              Aucune vente enregistrée pour le moment.
            </div>

            <div v-else-if="!loading">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Date</th>
                      <th>Client</th>
                      <th>Produit</th>
                      <th>Quantité</th>
                      <th>Prix unitaire</th>
                      <th>Montant total</th>
                      <th>Vendeur</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="vente in ventes" :key="vente.id_vente">
                      <td>{{ vente.id_vente }}</td>
                      <td>{{ formatDate(vente.date_vente) }}</td>
                      <td>{{ vente.client }}</td>
                      <td>{{ vente.produit }}</td>
                      <td>{{ vente.quantite }}</td>
                      <td>{{ vente.prix_unitaire }} $</td>
                      <td>{{ vente.montant_total }} $</td>
                      <td>{{ vente.vendeur }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-between mt-3">
                <div>
                  <p>
                    Total des ventes: <strong>{{ totalVentes }}</strong>
                  </p>
                </div>
                <div>
                  <button
                    class="btn btn-info"
                    @click="generateFicheJournaliere"
                  >
                    <i class="fas fa-file-pdf"></i> Fiche Journalière
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from "vue";
import { useVenteStore } from "src/stores/VenteStore";
import { useCommandeStore } from "src/stores/CommandeStore";
import { format } from "date-fns";

export default defineComponent({
  name: "VentePage",
  setup() {
    const venteStore = useVenteStore();
    const commandeStore = useCommandeStore();

    const newVente = ref({
      id_commande: "",
      quantite: 1,
      date_vente: format(new Date(), "yyyy-MM-dd"),
    });

    const selectedCommande = ref(null);

    // Charger les données initiales
    onMounted(async () => {
      await venteStore.fetchVentes();
      await venteStore.fetchTotalVentes();
      await commandeStore.fetchCommandes();
    });

    // Computed properties
    const ventes = computed(() => venteStore.getVentes);
    const totalVentes = computed(() => venteStore.getTotalVentes);
    const loading = computed(() => venteStore.isLoading);
    const error = computed(() => venteStore.getError);

    const commandesDisponibles = computed(() => {
      return commandeStore.getCommandes.filter((cmd) => cmd.quantite > 0);
    });

    const quantiteMax = computed(() => {
      if (!selectedCommande.value) return 0;
      return selectedCommande.value.quantite;
    });

    // Méthodes
    const loadCommandeInfo = async () => {
      if (!newVente.value.id_commande) {
        selectedCommande.value = null;
        return;
      }

      const cmd = commandesDisponibles.value.find(
        (c) => c.id === newVente.value.id_commande
      );
      selectedCommande.value = cmd ? { ...cmd } : null;

      // Ajuster la quantité si nécessaire
      if (
        selectedCommande.value &&
        newVente.value.quantite > selectedCommande.value.quantite
      ) {
        newVente.value.quantite = selectedCommande.value.quantite;
      }
    };

    const submitVente = async () => {
      try {
        const result = await venteStore.saveVente(newVente.value);
        if (result.succes) {
          // Réinitialiser le formulaire
          newVente.value = {
            id_commande: "",
            quantite: 1,
            date_vente: format(new Date(), "yyyy-MM-dd"),
          };
          selectedCommande.value = null;

          // Actualiser les commandes
          await commandeStore.fetchCommandes();
        }
      } catch (err) {
        console.error("Erreur lors de l'enregistrement:", err);
      }
    };

    const fetchVentes = async () => {
      await venteStore.fetchVentes();
    };

    const formatDate = (dateStr) => {
      return format(new Date(dateStr), "dd/MM/yyyy");
    };

    const generateFicheJournaliere = async () => {
      try {
        const fiche = await venteStore.fetchFicheJournaliere();
        // Ici vous pourriez générer un PDF ou afficher la fiche dans un modal
        console.log("Fiche journalière:", fiche);
        alert("Fiche journalière générée (voir console)");
      } catch (err) {
        console.error("Erreur génération fiche:", err);
      }
    };

    return {
      newVente,
      selectedCommande,
      ventes,
      totalVentes,
      loading,
      error,
      commandesDisponibles,
      quantiteMax,
      loadCommandeInfo,
      submitVente,
      fetchVentes,
      formatDate,
      generateFicheJournaliere,
    };
  },
});
</script>

<style scoped>
.container-fluid {
  background-color: #f4f6f9;
  padding: 25px;
  border-radius: 10px;
}

/* Titres */
h2 {
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 20px;
}

/* Cartes */
.card {
  margin-bottom: 20px;
  border-radius: 12px;
  border: none;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card:hover {
  transform: translateY(-3px) scale(1.01);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

/* En-têtes de carte */
.card-header {
  padding: 14px 20px;
  font-weight: bold;
  font-size: 1.15rem;
  border-bottom: none;
  letter-spacing: 0.3px;
}
.bg-primary {
  background: linear-gradient(135deg, #007bff, #0056b3) !important;
  color: #fff;
}
.bg-secondary {
  background: linear-gradient(135deg, #6c757d, #495057) !important;
  color: #fff;
}

/* Formulaires */
.form-control {
  border-radius: 8px;
  padding: 10px 12px;
  border: 1px solid #ced4da;
  transition: border-color 0.25s ease, box-shadow 0.25s ease;
}
.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
}

/* Boutons */
.btn {
  border-radius: 8px;
  padding: 9px 18px;
  font-weight: 500;
  transition: all 0.25s ease;
}
.btn:hover {
  opacity: 0.95;
  transform: translateY(-1px);
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
}

/* Tableau amélioré */
.table {
  border-radius: 8px;
  overflow: hidden;
  border-collapse: separate;
  border-spacing: 0;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* En-tête du tableau */
.table th {
  background: linear-gradient(135deg, #e9ecef, #dee2e6);
  color: #495057;
  font-weight: 600;
  padding: 12px;
  text-align: left;
  border-bottom: 2px solid #dee2e6;
  position: sticky;
  top: 0;
  z-index: 2;
}

/* Lignes du tableau */
.table td {
  padding: 10px 12px;
  border-bottom: 1px solid #f1f3f5;
  vertical-align: middle;
}

/* Alternance de lignes */
.table tbody tr:nth-child(even) {
  background-color: #f8f9fa;
}

/* Effet hover plus net */
.table-hover tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.12);
  transition: background-color 0.2s ease;
}

/* Bordures arrondies pour le tableau */
.table thead tr:first-child th:first-child {
  border-top-left-radius: 8px;
}
.table thead tr:first-child th:last-child {
  border-top-right-radius: 8px;
}
.table tbody tr:last-child td:first-child {
  border-bottom-left-radius: 8px;
}
.table tbody tr:last-child td:last-child {
  border-bottom-right-radius: 8px;
}

/* Alertes */
.alert {
  border-radius: 8px;
  font-size: 0.95rem;
  padding: 12px 16px;
}

/* Spinner */
.spinner-border {
  display: inline-block;
  width: 1.8rem;
  height: 1.8rem;
  vertical-align: text-bottom;
  border: 0.25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spinner-border 0.75s linear infinite;
}
@keyframes spinner-border {
  to {
    transform: rotate(360deg);
  }
}

</style>
