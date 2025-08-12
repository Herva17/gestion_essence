<template>
  <q-layout view="hHh lpR lFf">
    <!-- En-tête -->
    <q-header class="blondy text-white">
      <q-toolbar>
        <!-- Bouton menu -->
        <q-btn
          flat dense round
          icon="menu"
          aria-label="Menu"
          color="black"
          class="bg-grey-3 custom-border"
          @click="toggleLeftDrawer"
        />

        <!-- Titre (visible sur desktop) -->
        <q-toolbar-title class="q-ml-sm">
          <div v-if="$q.platform.is.desktop">
            Gestion Commande - Station KMJ
          </div>
        </q-toolbar-title>

        <!-- Boutons de droite -->
        <div class="row items-center q-gutter-x-sm">
          <!-- Bouton mode sombre/clair -->
          <q-btn
            flat round
            @click="$q.dark.toggle()"
            color="white"
            :icon="$q.dark.isActive ? 'nights_stay' : 'wb_sunny'"
          />

          <!-- Bouton déconnexion -->
          <q-btn 
            no-caps flat 
            class="bg-grey-3 custom-border" 
            to="/"
          >
            <q-icon size="xs" name="logout" class="text-orange" />
            <span class="text-black q-ml-sm">Déconnexion</span>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <!-- Menu latéral -->
    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      :width="270"
      :mini="miniState"
      show-if-above
      @mouseover="miniState = false"
      @mouseout="miniState = true"
      :class="$q.dark.isActive ? 'bg-dark' : 'bg-grey-2'"
    >
      <!-- En-tête du drawer -->
      <div class="q-px-md q-py-md row items-center">
        <q-avatar size="md" class="bg-primary text-white q-mr-sm">📝</q-avatar>
        <q-toolbar-title class="text-weight-bold">
          Gestion Commande
        </q-toolbar-title>
      </div>
      
      <q-separator />

      <!-- Liste des menus -->
      <q-list padding class="menu-list">
        <!-- Menu principal -->
        <q-item
          v-for="item in mainMenuItems"
          :key="item.to"
          :to="item.to"
          exact
          clickable
          v-ripple
          :active="activeLink === item.name"
          @click="updateActiveLink(item.name)"
          :class="{ 'active-menu-item': activeLink === item.name }"
        >
          <q-item-section avatar>
            <q-icon :name="item.icon" />
          </q-item-section>
          <q-item-section>{{ item.label }}</q-item-section>
        </q-item>

        <!-- Section Rapports -->
        <q-separator spaced />
        <q-expansion-item
          icon="assessment"
          label="Rapports"
          :content-inset-level="0.5"
          :class="{ 'active-expansion': activeLink.startsWith('rapports') }"
          default-opened
        >
          <q-item
            v-for="report in reportItems"
            :key="report.to"
            :to="report.to"
            exact
            clickable
            v-ripple
            :active="activeLink === report.name"
            @click="updateActiveLink(report.name)"
            :class="{ 'active-submenu-item': activeLink === report.name }"
            class="submenu-item"
          >
            <q-item-section avatar>
              <q-icon :name="report.icon" />
            </q-item-section>
            <q-item-section>{{ report.label }}</q-item-section>
          </q-item>
        </q-expansion-item>

        <!-- Section Aide -->
        <q-separator spaced />
        <q-item
          to="/help"
          exact
          clickable
          v-ripple
          :active="activeLink === 'help'"
          @click="updateActiveLink('help')"
          :class="{ 'active-menu-item': activeLink === 'help' }"
        >
          <q-item-section avatar>
            <q-icon name="help" />
          </q-item-section>
          <q-item-section>Aide</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- Contenu principal avec transition -->
    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const leftDrawerOpen = ref(false)
const miniState = ref(false)
const activeLink = ref('dashboard')

// Items du menu principal
const mainMenuItems = [
  { name: 'dashboard', to: '/com/commande-dashboard', icon: 'dashboard', label: 'Dashboard' },
  { name: 'client', to: '/com/client-form', icon: 'people', label: 'Client' },
  { name: 'commande', to: '/com/commande-form', icon: 'shopping_cart', label: 'Commande' },
  { name: 'vente', to: '/com/commande-vente', icon: 'point_of_sale', label: 'Ventes' },
  { name: 'mouvement', to: '/com/mouvement-form', icon: 'swap_horiz', label: 'Mouvement Stock' }
]

// Items des rapports
const reportItems = [
  { name: 'rapports-fiche', to: '/rapport', icon: 'inventory', label: 'Fiche de stocks' },
  { name: 'rapports-journal', to: '/journal-vente', icon: 'receipt', label: 'Journal des ventes' }
]

// Charger le lien actif depuis le localStorage
const loadActiveLink = () => {
  try {
    return localStorage.getItem('activeLinkCommande') || 'dashboard'
  } catch (error) {
    console.error("Erreur lors du chargement du lien:", error)
    return 'dashboard'
  }
}

// Sauvegarder le lien actif
const saveActiveLink = (link) => {
  try {
    localStorage.setItem('activeLinkCommande', link)
  } catch (error) {
    console.error("Erreur lors de la sauvegarde du lien:", error)
  }
}

// Mettre à jour le lien actif
const updateActiveLink = (newLink) => {
  activeLink.value = newLink
  saveActiveLink(newLink)
}

// Initialisation
onMounted(() => {
  activeLink.value = loadActiveLink()
  
  // Redirection si nécessaire
  if (router.currentRoute.value.path === '/com') {
    router.push(`/com/${activeLink.value}`)
  }
})

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}
</script>

<style lang="scss" scoped>
// Style de l'en-tête
.blondy {
  background: linear-gradient(135deg, #003973 0%, #E5E5BE 100%);
}

// Style du menu
.menu-list {
  .q-item {
    border-radius: 8px;
    margin: 4px 8px;
    transition: all 0.3s ease;

    &:hover {
      background-color: rgba(0, 57, 115, 0.1);
    }
  }

  .active-menu-item {
    color: white;
    background: linear-gradient(to right, #E5E5BE, #003973);
    
    .q-icon {
      color: white;
    }
  }

  .active-expansion {
    color: white;
    background: linear-gradient(to right, #ff6f00, #4a148c);
    
    .q-icon {
      color: white;
    }
  }

  .submenu-item {
    padding-left: 56px;
  }

  .active-submenu-item {
    color: white;
    background: linear-gradient(to right, #ff9e00, #6a1b9a);
    
    .q-icon {
      color: white;
    }
  }
}

// Style du drawer en mode sombre
.body--dark {
  .menu-list {
    .q-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  }
}

// Style du bouton custom
.custom-border {
  border-radius: 8px;
  border: 1px solid rgba(0, 0, 0, 0.12);
}

// Transition entre les pages
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

// Style pour l'expansion item
.q-expansion-item__content {
  background-color: rgba(0, 0, 0, 0.03) !important;
}

.q-item__section--avatar {
  min-width: 40px !important;
}
</style>