<template>
  <div class="q-pa-md mouvement-form-bg">
    <q-card class="q-pa-lg mouvement-form-card" style="width: 100%">
      <!-- Bandeau titre -->
      <div class="mouvement-title-bar q-pa-md q-mb-md">
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="swap_horiz" class="q-mr-sm" /> Gestion des Mouvements de
          Stock
        </div>
      </div>
      <!-- Fin bandeau titre -->
      <div class="row q-col-gutter-md q-mb-md">
        <div class="col-12 col-md-6">
          <q-card class="dashboard-card bg-green-2">
            <q-card-section class="row items-center">
              <q-icon
                name="login"
                color="positive"
                size="36px"
                class="q-mr-md"
              />
              <div>
                <div class="text-h6 text-weight-bold">{{ nbEntree }}</div>
                <div class="text-subtitle2 text-grey-7">Produits Entrants</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-md-6">
          <q-card class="dashboard-card bg-red-1">
            <q-card-section class="row items-center">
              <q-icon
                name="logout"
                color="negative"
                size="36px"
                class="q-mr-md"
              />
              <div>
                <div class="text-h6 text-weight-bold">{{ nbSortie }}</div>
                <div class="text-subtitle2 text-grey-7">Produits Sortants</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
      <q-card-section>
        <div class="row items-center justify-between">
          <div class="text-h5 text-primary text-weight-bold">
            Liste des Mouvements
          </div>
          <div>
            <q-btn
              color="secondary"
              icon="print"
              label="Imprimer rapport"
              class="q-mr-sm"
              @click="imprimerRapport"
            />
          </div>
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-table
          :rows="mouvements"
          :columns="columns"
          row-key="id"
          flat
          bordered
          :pagination="{ rowsPerPage: 5 }"
          no-data-label="Aucun mouvement enregistré"
        >
          <template #body-cell-actions="props">
            <q-td align="center">
              <q-btn
                dense
                flat
                round
                icon="edit"
                color="primary"
                @click="editMouvement(props.row)"
              />
              <q-btn
                dense
                flat
                round
                icon="delete"
                color="negative"
                @click="deleteMouvement(props.row.id)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Modal de modification -->
      <q-dialog v-model="showEditModal">
        <q-card style="min-width: 350px">
          <q-card-section>
            <div class="text-h6">Modifier le Mouvement</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onUpdate" class="q-gutter-md">
              <q-input
                v-model="editMouvementData.designation"
                label="Produit"
                outlined
                dense
                required
                color="primary"
              />
              <q-input
                v-model="editMouvementData.type"
                label="Type de mouvement (Entrée/Sortie)"
                outlined
                dense
                required
                color="primary"
              />
              <q-input
                v-model="editMouvementData.quantite"
                label="Quantité"
                type="number"
                outlined
                dense
                required
                color="primary"
                min="1"
              />
              <q-input
                v-model="editMouvementData.date_mouvement"
                label="Date"
                type="date"
                outlined
                dense
                required
                color="primary"
              />
              <div class="row justify-end">
                <q-btn
                  label="Mettre à jour"
                  color="primary"
                  type="submit"
                  icon="save"
                  unelevated
                />
                <q-btn
                  flat
                  label="Annuler"
                  color="grey"
                  class="q-ml-sm"
                  v-close-popup
                />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
    </q-card>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { useQuasar } from "quasar";
import { useMouvementStore } from "src/stores/MouvementStore";

const $q = useQuasar();
const mouvementStore = useMouvementStore();

const showEditModal = ref(false);

const editMouvementData = ref({
  id: null,
  designation: "",
  type: "",
  quantite: "",
  date_mouvement: "",
});

// Utilise les mouvements du store
const mouvements = computed(() => mouvementStore.mouvements);

const columns = [
  {
    name: "designation",
    label: "Produit",
    field: "designation",
    align: "left",
  },
  { name: "type", label: "Type", field: "type", align: "left" },
  { name: "quantite", label: "Quantité", field: "quantite", align: "right" },
  {
    name: "date_mouvement",
    label: "Date",
    field: "date_mouvement",
    align: "left",
  },
  {
    name: "actions",
    label: "Actions",
    field: "actions",
    align: "center",
    sortable: false,
  },
];

// Charger les mouvements à l'ouverture de la page
onMounted(() => {
  mouvementStore.fetchMouvements();
});

function deleteMouvement(id) {
  mouvementStore.mouvements = mouvementStore.mouvements.filter(
    (m) => m.id !== id
  );
  $q.notify({ type: "negative", message: "Mouvement supprimé." });
}

function editMouvement(row) {
  editMouvementData.value = { ...row };
  showEditModal.value = true;
}

function onUpdate() {
  const idx = mouvementStore.mouvements.findIndex(
    (m) => m.id === editMouvementData.value.id
  );
  if (idx !== -1) {
    mouvementStore.mouvements[idx] = { ...editMouvementData.value };
    $q.notify({ type: "positive", message: "Mouvement modifié !" });
  }
  showEditModal.value = false;
}

const nbEntree = computed(
  () =>
    mouvements.value.filter(
      (m) =>
        m.type.toLowerCase() === "entrée" || m.type.toLowerCase() === "entree"
    ).length
);
const nbSortie = computed(
  () => mouvements.value.filter((m) => m.type.toLowerCase() === "sortie").length
);

function imprimerRapport() {
  window.print();
}
</script>

<style scoped>
.mouvement-form-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.mouvement-form-card {
  max-width: 1200px;
  margin: auto;
  border-radius: 18px;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1), 0 1.5px 4px 0 rgba(0, 0, 0, 0.08);
}
.mouvement-title-bar {
  background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
  border-radius: 14px 14px 0 0;
  text-align: left;
  display: flex;
  align-items: center;
}
.dashboard-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.06);
}
</style>
