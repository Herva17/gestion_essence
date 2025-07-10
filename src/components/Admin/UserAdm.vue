<template>
  <q-table
    class="min-w-full border-gray-200 square"
    title="Nos Utilisateurs"
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
        @click="addUser"
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
            @click="editUser(props.row)"
          />
          <q-btn
            dense
            outline
            size="sm"
            color="red"
            icon="delete"
            @click="deleteUser(props.row.id)"
          />
        </div>
      </q-td>
    </template>
  </q-table>
  <q-dialog v-model="addEditUser" :maximized="$q.platform.is.mobile">
    <q-card
      :style="
        $q.platform.is.desktop ? { width: '500px', 'max-width': '50vw' } : {}
      "
    >
      <q-card-section class="q-pt-md">
        <q-form class="q-gutter-md" @submit.prevent="saveUser">
          <q-list>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">ID</q-item-label>
                <q-input dense outlined v-model="user.id" disable />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Nom</q-item-label>
                <q-input dense outlined v-model="user.nom" />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Prénom</q-item-label>
                <q-input dense outlined v-model="user.prenom" />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Email</q-item-label>
                <q-input dense outlined v-model="user.email" />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Téléphone</q-item-label>
                <q-input dense outlined v-model="user.telephone" />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Adresse</q-item-label>
                <q-input dense outlined v-model="user.adresse" />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Sexe</q-item-label>
                <q-select
                  :options="[
                    { value: 'M', label: 'Masculin' },
                    { value: 'F', label: 'Féminin' },
                  ]"
                  dense
                  outlined
                  v-model="user.sexe"
                />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Mot de passe</q-item-label>
                <q-input
                  dense
                  outlined
                  v-model="user.mot_de_passe"
                  type="password"
                />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label class="q-pb-xs">Rôle</q-item-label>
                <q-select :options="les_cat" dense outlined v-model="Id_cat" />
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
import { useQuasar } from "quasar";

const $q = useQuasar();
const utilisateurStore = useUtilisateurStore();
onMounted(() => utilisateurStore.fetchUtilisateurs());
const utilisateurs = computed(() => utilisateurStore.utilisateurs);

const les_cat = ref([
  { value: "Admin", label: "Administrateur" },
  { value: "Gerant", label: "Gérant" },
  { value: "Comptable", label: "Comptable" },
]);

const filter = ref("");
const Id_cat = ref(null);
const user = ref({
  id: "",
  nom: "",
  prenom: "",
  email: "",
  telephone: "",
  adresse: "",
  sexe: "",
  mot_de_passe: "",
  role: "",
});
const addEditUser = ref(false);
const addFlag = ref(true);
const mode = ref("list");
const columns = [
  { name: "nom", align: "left", label: "Nom", field: "nom", sortable: true },
  { name: "prenom", align: "left", label: "Prénom", field: "prenom", sortable: true },
  { name: "email", align: "left", label: "Email", field: "email", sortable: true },
  { name: "telephone", align: "left", label: "Téléphone", field: "telephone", sortable: true },
  { name: "adresse", align: "left", label: "Adresse", field: "adresse", sortable: true },
  { name: "role", align: "left", label: "Rôle", field: "role", sortable: true },
  { name: "action", align: "left", label: "Action", field: "action", sortable: false },
];

const pagination = ref({ rowsPerPage: 10 });

function addUser() {
  addFlag.value = true;
  user.value = {
    id: "",
    nom: "",
    prenom: "",
    email: "",
    telephone: "",
    adresse: "",
    sexe: "",
    mot_de_passe: "",
    role: "",
  };
  Id_cat.value = null;
  addEditUser.value = true;
}

function editUser(val) {
  addFlag.value = false;
  user.value = { ...val };
  Id_cat.value = les_cat.value.find((cat) => cat.value === val.role) || null;
  addEditUser.value = true;
}

async function saveUser() {
  try {
    user.value.role = Id_cat.value ? Id_cat.value.value : "";
    if (!user.value.role) {
      $q.notify({ type: "negative", message: "Veuillez choisir un rôle." });
      return;
    }
    if (addFlag.value) {
      await utilisateurStore.addUtilisateur(user.value);
      $q.notify({
        type: "positive",
        message: "Utilisateur ajouté avec succès !",
      });
    } else {
      await utilisateurStore.modifierUtilisateur(user.value);
      $q.notify({
        type: "positive",
        message: "Modification enregistrée !",
      });
    }
    addEditUser.value = false;
  } catch (e) {
    console.log(e);
    $q.notify({
      type: "negative",
      message: "Erreur lors de l'enregistrement !",
    });
  }
}

async function deleteUser(id) {
  try {
    await utilisateurStore.supprimerUtilisateur(id);
    $q.notify({
      type: "positive",
      message: "Utilisateur supprimé avec succès !",
    });
  } catch (e) {
    console.log(e);
    $q.notify({
      type: "negative",
      message: "Erreur lors de la suppression !",
    });
  }
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
