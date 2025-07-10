<template>
  <div class="q-pa-md produit-dashboard-bg">
    <div class="text-h4 text-center text-weight-bold q-mb-lg dashboard-title">
      <q-icon name="dashboard" class="q-mr-sm" />Tableau de bord Commande
      <span class="text-grey-5 text-h6 q-ml-md">| STATISTIQUES</span>
    </div>
    <div class="row q-col-gutter-xl q-row-gutter-md justify-center">
      <!-- Carte Clients -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card
          class="pro-card bg-gradient-blue cursor-pointer"
          @click="$router.push({ name: 'client-table' })"
        >
          <q-card-section class="flex items-center">
            <q-avatar
              size="56px"
              class="q-mr-md bg-white text-blue-600 shadow-2"
            >
              <q-icon name="people" size="32px" />
            </q-avatar>
            <div>
              <div class="text-h5 text-white text-weight-bold">
                {{ nbClients }}
              </div>
              <div class="text-white text-subtitle2">Clients</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Carte Commandes -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card
          class="pro-card bg-gradient-green cursor-pointer"
          @click="$router.push({ name: 'commande-table' })"
        >
          <q-card-section class="flex items-center">
            <q-avatar
              size="56px"
              class="q-mr-md bg-white text-green-600 shadow-2"
            >
              <q-icon name="shopping_cart" size="32px" />
            </q-avatar>
            <div>
              <div class="text-h5 text-white text-weight-bold">
                {{ nbCommandes }}
              </div>
              <div class="text-white text-subtitle2">Commandes</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Carte Produits -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card
          class="pro-card bg-gradient-purple cursor-pointer"
          @click="$router.push({ name: 'produit-form' })"
        >
          <q-card-section class="flex items-center">
            <q-avatar
              size="56px"
              class="q-mr-md bg-white text-purple-600 shadow-2"
            >
              <q-icon name="inventory_2" size="32px" />
            </q-avatar>
            <div>
              <div class="text-h5 text-white text-weight-bold">
                {{ nbProduits }}
              </div>
              <div class="text-white text-subtitle2">Produits</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Carte Mouvement Stock -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card
          class="pro-card bg-gradient-red cursor-pointer"
          @click="$router.push({ name: 'mouvement-form' })"
        >
          <q-card-section class="flex items-center">
            <q-avatar
              size="56px"
              class="q-mr-md bg-white text-red-600 shadow-2"
            >
              <q-icon name="swap_horiz" size="32px" />
            </q-avatar>
            <div>
              <div class="text-h5 text-white text-weight-bold">
                {{ nbMouvements }}
              </div>
              <div class="text-white text-subtitle2">Mouvements</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { useProduitStore } from "src/stores/ProduitStore";
import { useMouvementStore } from "src/stores/MouvementStore";
import { useClientStore } from "src/stores/ClientStore";
import { useCommandeStore } from "src/stores/CommandeStore";

const produitStore = useProduitStore();
const mouvementStore = useMouvementStore();
const clientStore = useClientStore();
const commandeStore = useCommandeStore();

onMounted(() => {
  produitStore.fetchTotalProduits();
  mouvementStore.fetchTotalMouvements();
  // clientStore.fetchTotalClients();
  commandeStore.fetchTotalCommandes();
});

const nbProduits = computed(() => produitStore.totalProduits);
const nbCommandes = computed(() => commandeStore.totalCommandes);
const nbClients = computed(() => clientStore.totalClients);
const nbMouvements = computed(() => mouvementStore.totalMouvements);
</script>
<style scoped>
.produit-dashboard-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0e7ef 0%, #f0f4f8 100%);
}
.dashboard-title {
  letter-spacing: 1px;
}
.bg-gradient-blue {
  background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
}
.bg-gradient-green {
  background: linear-gradient(90deg, #059669 0%, #34d399 100%);
}
.bg-gradient-purple {
  background: linear-gradient(90deg, #7c3aed 0%, #c084fc 100%);
}
.bg-gradient-red {
  background: linear-gradient(90deg, #dc2626 0%, #f87171 100%);
}
.pro-card {
  border-radius: 18px;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1), 0 1.5px 4px 0 rgba(0, 0, 0, 0.08);
  transition: transform 0.15s;
}
.pro-card:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 12px 36px 0 rgba(0, 0, 0, 0.13), 0 2px 8px 0 rgba(0, 0, 0, 0.1);
}
</style>
