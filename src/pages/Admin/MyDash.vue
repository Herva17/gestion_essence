<template>
  <q-layout view="hHh lpR lFf">
    <q-header class="blondy text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          color="black"
          class="bg-grey-3 custom-border"
          @click="toggleLeftDrawer"
        />
        <q-toolbar-title class="q-ml-sm">
          <div v-if="$q.platform.is.desktop"></div>
        </q-toolbar-title>

        <div>
          <q-btn
            class="q-mr-xs q-py-xs q-px-sm"
            flat
            @click="$q.dark.toggle()"
            color="white"
            :icon="$q.dark.isActive ? 'nights_stay' : 'wb_sunny'"
          />
        </div>

        <div class="q-mr-xs">
          <q-btn no-caps flat class="bg-grey-3 custom-border" to="/">
            <q-icon size="xs" style="color: rgb(250, 108, 14)" name="logout" />
            <span class="text-black q-ml-sm">Se déconnecter</span>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      :width="270"
      :mini="miniState"
      show-if-above
    >
      <div
        :class="$q.dark.isActive ? 'drawer_dark' : 'drawer_normal'"
        class="full-height q-px-sm"
      >
        <q-toolbar class="q-px-md q-py-md">
          <q-avatar
            class="q-pt-xs"
            color="primary"
            text-color="white"
            icon="store"
          />
          <q-toolbar-title class="q-pt-sm text-lg font-semibold">
            Gestion Stock
          </q-toolbar-title>
        </q-toolbar>
        <hr />

        <q-list class="rounded-borders text-black">
          <q-separator spaced />
          <div class="q-pl-md q-pb-xs text-grey-8 text-caption">
            Gestion Produit
          </div>

          <q-item
            to="/Main/produit-page"
            exact
            clickable
            v-ripple
            :active="link === 'dashboard'"
            @click="link = 'dashboard'"
            :class="{ 'my-menu-link': link === 'dashboard' }"
          >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Dashboard</q-item-section>
          </q-item>

          <q-item
            to="/Main/produit-form"
            exact
            clickable
            v-ripple
            :active="link === 'produit'"
            @click="link = 'produit'"
            :class="{ 'my-menu-link': link === 'produit' }"
          >
            <q-item-section avatar>
              <q-icon name="inventory_2" />
            </q-item-section>
            <q-item-section>Approvisionnement</q-item-section>
          </q-item>

          <q-item
            to="/Main/Categorie-Form"
            exact
            clickable
            v-ripple
            :active="link === 'categorie'"
            @click="link = 'categorie'"
            :class="{ 'my-menu-link': link === 'categorie' }"
          >
            <q-item-section avatar>
              <q-icon name="category" />
            </q-item-section>
            <q-item-section>produit</q-item-section>
          </q-item>

          <q-item
            to="/Main/Mouvement-Form"
            exact
            clickable
            v-ripple
            :active="link === 'mouvement'"
            @click="link = 'mouvement'"
            :class="{ 'my-menu-link': link === 'mouvement' }"
          >
            <q-item-section avatar>
              <q-icon name="swap_horiz" />
            </q-item-section>
            <q-item-section>Mouvement</q-item-section>
          </q-item>

                    <q-separator spaced />
          <div class="q-pl-md q-pb-xs text-grey-8 text-caption">
            Gestion Commande
          </div>
          <q-item
            to="/Main/client-form"
            exact
            clickable
            v-ripple
            :active="link === 'client'"
            @click="updateLink('client')"
            :class="{ 'my-menu-link': link === 'client' }"
          >
            <q-item-section avatar>
              <q-icon name="people" />
            </q-item-section>
            <q-item-section>Client</q-item-section>
          </q-item>
          <q-item
            to="/Main/commande-form"
            exact
            clickable
            v-ripple
            :active="link === 'commande'"
            @click="updateLink('commande')"
            :class="{ 'my-menu-link': link === 'commande' }"
          >
            <q-item-section avatar>
              <q-icon name="shopping_cart" />
            </q-item-section>
            <q-item-section>Commande</q-item-section>
          </q-item>

          <q-item
            to="/Main/commande-vente"
            exact
            clickable
            v-ripple
            :active="link === 'vente'"
            @click="updateLink('vente')"
            :class="{ 'my-menu-link': link === 'commande' }"
          >
            <q-item-section avatar>
              <q-icon name="shopping_cart" />
            </q-item-section>
            <q-item-section>Ventes</q-item-section>
          </q-item>
      <!-- Section Rapports ajoutée ici -->
          <q-separator spaced />
          <div class="q-pl-md q-pb-xs text-grey-8 text-caption">
            Rapports
          </div>

          <q-expansion-item
            icon="assessment"
            label="Rapports"
            :content-inset-level="0.5"
            :class="{ 'my-menu-link-rapports': link.startsWith('rapports') }"
          >
            <q-item
              to="/rapport"
              exact
              clickable
              v-ripple
              :active="link === 'rapports-fiche'"
              @click="link = 'rapports-fiche'"
              :class="{ 'my-submenu-link': link === 'rapports-fiche' }"
            >
              <q-item-section avatar>
                <q-icon name="inventory" />
              </q-item-section>
              <q-item-section>Fiche de stocks</q-item-section>
            </q-item>

            <q-item
              to="/journal-vente"
              exact
              clickable
              v-ripple
              :active="link === 'rapports-journal'"
              @click="link = 'rapports-journal'"
              :class="{ 'my-submenu-link': link === 'rapports-journal' }"
            >
              <q-item-section avatar>
                <q-icon name="receipt" />
              </q-item-section>
              <q-item-section>Journal des ventes</q-item-section>
            </q-item>
          </q-expansion-item>


          <q-separator spaced />
          <div class="q-pl-md q-pb-xs text-grey-8 text-caption">
            Gestion Utilisateurs
          </div>

          <q-item
            to="/Main/Dash"
            exact
            clickable
            v-ripple
            :active="link === 'parametres'"
            @click="link = 'parametres'"
            :class="{ 'my-menu-link': link === 'parametres' }"
          >
            <q-item-section avatar>
              <q-icon name="settings" />
            </q-item-section>
            <q-item-section>Paramètres</q-item-section>
          </q-item>

          <q-item
            to=""
            exact
            clickable
            v-ripple
            :active="link === 'aide'"
            @click="link = 'aide'"
            :class="{ 'my-menu-link': link === 'aide' }"
          >
            <q-item-section avatar>
              <q-icon name="help" />
            </q-item-section>
            <q-item-section>Aide</q-item-section>
          </q-item>
        </q-list>
      </div>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref, watch } from "vue";

export default {
  setup() {
    const leftDrawerOpen = ref(false);
    const miniState = ref(false);
    const link = ref("dashboard");

    // Surveillez les modifications dans le lien actif et stockez-les dans localStorage
    watch(link, (newValue) => {
      localStorage.setItem("activeLink", newValue);
    });

    // Récupère le lien actif depuis localStorage lors du montage du composant
    link.value = localStorage.getItem("activeLink") || "dashboard";

    // Ajout de la fonction updateLink pour éviter l'erreur
    function updateLink(val) {
      link.value = val;
    }

    return {
      leftDrawerOpen,
      miniState,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
      link,
      updateLink, // expose la fonction au template
    };
  },
};
</script>

<style lang="sass">
.my-menu-link
  color: white
  background: #003973
  background: -webkit-linear-gradient(to right, #E5E5BE, #003973)
  background: linear-gradient(to right, #E5E5BE, #003973)

.my-menu-link-rapports
  color: white !important
  background: #4a148c
  background: -webkit-linear-gradient(to right, #ff6f00, #4a148c)
  background: linear-gradient(to right, #ff6f00, #4a148c)

.my-submenu-link
  color: white !important
  background: #6a1b9a
  background: -webkit-linear-gradient(to right, #ff9e00, #6a1b9a)
  background: linear-gradient(to right, #ff9e00, #6a1b9a)

.custom-border
  border-radius: 8px
  border: 1px solid #e0e0e0

.drawer_dark
  background-color: #1e1e1e

.drawer_normal
  background-color: #f5f5f5

/* Style pour l'expansion item */
.q-expansion-item__content
  background-color: rgba(0,0,0,0.03) !important

.q-item__section--avatar
  min-width: 40px !important
</style>
