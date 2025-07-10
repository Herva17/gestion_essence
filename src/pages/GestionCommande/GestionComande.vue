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
      @mouseover="miniState = false"
      @mouseout="miniState = true"
    >
      <div
        :class="$q.dark.isActive ? 'drawer_dark' : 'drawer_normal'"
        class="full-height q-px-sm"
      >
        <q-toolbar class="q-px-md q-py-md">
          <q-avatar class="q-pt-xs"> 📝 </q-avatar>
          <q-toolbar-title class="q-pt-sm text-lg font-semibold"
            >Gestion Commande</q-toolbar-title
          >
        </q-toolbar>
        <hr />

        <q-list class="rounded-borders text-black">
          <q-item
            to="/com/commande-dashboard"

            clickable
            v-ripple
            :active="link === 'dashboard'"
            @click="updateLink('dashboard')"
            :class="{ 'my-menu-link': link === 'dashboard' }"
          >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>Dashboard</q-item-section>
          </q-item>
          <q-item
            to="/com/client-form"
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
            to="/com/commande-form"
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
            to="/com/mouvement-form"
            exact
            clickable
            v-ripple
            :active="link === 'mouvement'"
            @click="updateLink('mouvement')"
            :class="{ 'my-menu-link': link === 'mouvement' }"
          >
            <q-item-section avatar>
              <q-icon name="swap_horiz" />
            </q-item-section>
            <q-item-section>Mouvement Stock</q-item-section>
          </q-item>
          <q-separator spaced />
          <q-item
            to=""
            exact
            clickable
            v-ripple
            :active="link === 'help'"
            @click="updateLink('help')"
            :class="{ 'my-menu-link': link === 'help' }"
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
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref, watch, onMounted } from "vue";
import { useRouter } from "vue-router";

export default {
  name: "GestionCommande",
  setup() {
    const router = useRouter();
    const leftDrawerOpen = ref(false);
    const miniState = ref(false);
    const link = ref("dashboard");

    const saveActiveLink = (newLink) => {
      try {
        localStorage.setItem("activeLinkCommande", newLink);
      } catch (error) {
        console.error("Erreur lors de la sauvegarde du lien:", error);
      }
    };

    const loadActiveLink = () => {
      try {
        const savedLink = localStorage.getItem("activeLinkCommande");
        return savedLink || "dashboard";
      } catch (error) {
        console.error("Erreur lors du chargement du lien:", error);
        return "dashboard";
      }
    };

    const updateLink = (newLink) => {
      link.value = newLink;
      saveActiveLink(newLink);
    };

    watch(link, (newValue) => {
      saveActiveLink(newValue);
    });

    onMounted(() => {
      const savedLink = loadActiveLink();
      link.value = savedLink;

      // Redirection initiale si nécessaire
      if (router.currentRoute.value.path === "/com") {
        router.push(`/com/${savedLink}`);
      }
    });

    const toggleLeftDrawer = () => {
      leftDrawerOpen.value = !leftDrawerOpen.value;
    };

    return {
      leftDrawerOpen,
      miniState,
      toggleLeftDrawer,
      link,
      updateLink
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

.custom-border
  border-radius: 8px
  border: 1px solid #e0e0e0

.drawer_dark
  background-color: #1e1e1e

.drawer_normal
  background-color: #f5f5f5

.fade-enter-active,
.fade-leave-active
  transition: opacity 0.3s ease

.fade-enter-from,
.fade-leave-to
  opacity: 0
</style>
