<template>
  <q-table
    class="min-w-full border-gray-200 square"
    title="Nos Utilisateurs Ayant accès à la Connexion"
    :rows="utilisateurs"
    :hide-header="mode === 'grid'"
    :columns="columns"
    row-key="id"
    :grid="mode == 'grid'"
    :filter="filter"
    v-model:pagination="pagination"
  >
    <template v-slot:top-right="props">
      <q-btn
        @click="addLog"
        flat
        size="lg"
        color="primary"
        icon="add_circle"
        class="q-mr-xs q-pa-none"
      />
      <q-input
        dense
        borderless
        v-model="filter"
        placeholder="Search"
        class="custom-border bg-grey-3 q-pl-sm"
        style="border-radius: 10px"
      >
        <template v-slot:append>
          <q-icon class="q-pr-sm" color="grey-8" name="search" />
        </template>
      </q-input>
      <q-btn
        flat
        round
        dense
        class="q-ml-xs"
        :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
        @click="props.toggleFullscreen"
        v-if="mode === 'list'"
      >
        <q-tooltip :disable="$q.platform.is.mobile" v-close-popup>
          {{ props.inFullscreen ? "Exit Fullscreen" : "Toggle Fullscreen" }}
        </q-tooltip>
      </q-btn>
    </template>
    <template v-slot:body-cell-action="props">
      <q-td :props="props">
        <div class="q-gutter-sm">
          <q-btn
            dense
            outline
            size="sm"
            color="primary"
            icon="edit"
            @click="editLog(props.row)"
          />
          <q-btn
            dense
            outline
            size="sm"
            color="red"
            icon="delete"
            @click="deleteLog(props.row.id_psw)"
          />
        </div>
      </q-td>
    </template>
  </q-table>
  <q-dialog v-model="addEditLog" :maximized="$q.platform.is.mobile">
    <q-card
      :style="
        $q.platform.is.desktop ? { width: '500px', 'max-width': '50vw' } : {}
      "
    >
      <q-card-section>
        <div class="text-h6 q-px-md">
          {{ addFlag ? "Ajouter Accès" : "Editer l'Accès" }}
          <q-btn
            round
            flat
            dense
            icon="close"
            class="float-right"
            color="grey-8"
            v-close-popup
          ></q-btn>
        </div>
      </q-card-section>
      <q-separator class="q-px-md" inset></q-separator>
      <q-card-section class="q-pt-md">
        <q-form class="q-gutter-md">
          <q-list>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">id</q-item-label>
                <q-input dense outlined v-model="log.id_psw" disable />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Utilisateur</q-item-label>
                <q-select
                  :options="les_users"
                  dense
                  outlined
                  v-model="Id_adm"
                />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">psw</q-item-label>
                <q-input dense outlined v-model="log.psw" />
              </q-item-section>
            </q-item>
          </q-list>
          <div class="q-px-md q-pt-sm">
            <q-btn
              class="full-width"
              label="Enregistrer"
              type="submit"
              color="primary"
              v-close-popup
              @click="saveLog"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useUtilisateurStore } from "src/stores/UtilisateurStore";

const utilisateurStore = useUtilisateurStore();
onMounted(() => utilisateurStore.fetchUtilisateurs());
const utilisateurs = computed(() => utilisateurStore.utilisateurs);

const filter = ref("");
const addEditLog = ref(false);
const addFlag = ref(true);
const mode = ref("list");
const log = ref({});
const Id_adm = ref(null);
const pagination = ref({ rowsPerPage: 10 });

// Colonnes adaptées à la structure de l'API
const columns = [
  { name: "nom", align: "left", label: "Nom", field: "nom", sortable: true },
  { name: "prenom", align: "left", label: "Prénom", field: "prenom", sortable: true },
  { name: "email", align: "left", label: "Email", field: "email", sortable: true },
  { name: "telephone", align: "left", label: "Téléphone", field: "telephone", sortable: true },
  { name: "role", align: "left", label: "Rôle", field: "role", sortable: true },
  { name: "action", align: "left", label: "Action", field: "action", sortable: false },
];

// Pour le select utilisateur dans la modale
const les_users = computed(() =>
  utilisateurs.value.map(u => ({
    value: u.id,
    label: `${u.nom} ${u.prenom}`,
  }))
);

function addLog() {
  addFlag.value = true;
  log.value = {};
  addEditLog.value = true;
}

function editLog(val) {
  addFlag.value = false;
  log.value = { ...val };
  addEditLog.value = true;
}

function saveLog() {
  // À adapter selon ton API si tu veux ajouter/modifier côté serveur
  addEditLog.value = false;
}

function deleteLog(id) {
  // À adapter selon ton API si tu veux supprimer côté serveur
  utilisateurStore.utilisateurs = utilisateurStore.utilisateurs.filter(u => u.id !== id);
}
</script>

<style scoped>
.custom-border {
  border-radius: 5px;
}

.custom-table {
  overflow: hidden;
}

.custom-table ::-webkit-scrollbar {
  display: none;
}

.custom-table {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
