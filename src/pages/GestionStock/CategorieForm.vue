<template>
  <div class="q-pa-md categorie-form-bg">
    <q-card class="q-pa-lg categorie-form-card" style="width: 100%">
      <!-- Bandeau titre -->
      <div class="categorie-title-bar q-pa-md q-mb-md">
        <div class="text-h5 text-white text-weight-bold">
          <q-icon name="category" class="q-mr-sm" /> Gestion des Catégories
        </div>
      </div>
      <!-- Fin bandeau titre -->

      <q-card-section>
        <div class="row items-center justify-between">
          <div class="text-h5 text-primary text-weight-bold">
            Liste des Catégories
          </div>
          <q-btn
            color="primary"
            icon="add"
            label="Ajouter Catégorie"
            @click="showAddModal = true"
          />
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-table
          :rows="categories"
          :columns="columns"
          row-key="id"
          flat
          bordered
          :pagination="{ rowsPerPage: 5 }"
          no-data-label="Aucune catégorie enregistrée"
          :loading="categorieStore.loading"
        >
          <template #body-cell-actions="props">
            <q-td align="center">
              <q-btn
                dense
                flat
                round
                icon="edit"
                color="primary"
                @click="editCategorie(props.row)"
              />
              <q-btn
                dense
                flat
                round
                icon="delete"
                color="negative"
                @click="deleteCategorie(props.row.id)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>

      <!-- Modal d'ajout -->
      <q-dialog v-model="showAddModal">
        <q-card style="min-width: 350px">
          <q-card-section>
            <div class="text-h6">Ajouter une Catégorie</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onSubmit" class="q-gutter-md">
              <q-input
                v-model="categorie.designation"
                label="Nom de la catégorie"
                outlined
                dense
                required
                :rules="[(val) => !!val || 'Le nom est requis']"
                color="primary"
                autofocus
              />
              <q-input
                v-model="categorie.description"
                label="Description"
                type="textarea"
                outlined
                dense
                color="primary"
                autogrow
              />
              <div class="row justify-end">
                <q-btn
                  label="Enregistrer"
                  color="primary"
                  type="submit"
                  icon="check"
                  unelevated
                />
                <q-btn
                  flat
                  label="Annuler"
                  color="grey"
                  class="q-ml-sm"
                  v-close-popup
                  @click="showAddModal = false"
                />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <!-- Modal de modification -->
      <q-dialog v-model="showEditModal">
        <q-card style="min-width: 350px">
          <q-card-section>
            <div class="text-h6">Modifier la Catégorie</div>
          </q-card-section>
          <q-card-section>
            <q-form @submit.prevent="onUpdate" class="q-gutter-md">
              <q-input
                v-model="editCategorieData.designation"
                label="Nom de la catégorie"
                outlined
                dense
                required
                :rules="[(val) => !!val || 'Le nom est requis']"
                color="primary"
                autofocus
              />
              <q-input
                v-model="editCategorieData.description"
                label="Description"
                type="textarea"
                outlined
                dense
                color="primary"
                autogrow
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
import { useCategorieStore } from "src/stores/CategorieStore";

const $q = useQuasar();
const categorieStore = useCategorieStore();

const showAddModal = ref(false);
const showEditModal = ref(false);

const categorie = ref({ designation: "", description: "" });
const editCategorieData = ref({ id: null, designation: "", description: "" });

const categories = computed(() => categorieStore.categories);

const columns = [
  { name: "designation", label: "Nom", field: "designation", align: "left" },
  {
    name: "description",
    label: "Description",
    field: "description",
    align: "left",
  },
  {
    name: "date_creation",
    label: "Date création",
    field: "date_creation",
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

onMounted(() => {
  categorieStore.fetchCategories();
});

async function onSubmit() {
  if (!categorie.value.designation) return;
  try {
    await categorieStore.saveCategorie({
      designation: categorie.value.designation,
      description: categorie.value.description,
    });
    $q.notify({ type: "positive", message: "Catégorie enregistrée !" });
    categorie.value.designation = "";
    categorie.value.description = "";
    showAddModal.value = false;
  } catch (e) {
    $q.notify({ type: "negative", message: "Erreur lors de l'enregistrement !" });
  }
}

async function deleteCategorie(id) {
  try {
    await categorieStore.deleteCategorie(id);
    $q.notify({ type: "positive", message: "Catégorie supprimée !" });
  } catch (e) {
    $q.notify({ type: "negative", message: "Erreur lors de la suppression !" });
  }
}

function editCategorie(row) {
  editCategorieData.value = { ...row };
  showEditModal.value = true;
}

function onUpdate() {
  const idx = categorieStore.categories.findIndex(
    (cat) => cat.id === editCategorieData.value.id
  );
  if (idx !== -1) {
    categorieStore.categories[idx] = { ...editCategorieData.value };
    $q.notify({ type: "positive", message: "Catégorie modifiée !" });
  }
  showEditModal.value = false;
}
</script>

<style scoped>
.categorie-form-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.categorie-form-card {
  max-width: 1200px;
  margin: auto;
  border-radius: 18px;
  box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1), 0 1.5px 4px 0 rgba(0, 0, 0, 0.08);
}
.categorie-title-bar {
  background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
  border-radius: 14px 14px 0 0;
  text-align: left;
  display: flex;
  align-items: center;
}
</style>
