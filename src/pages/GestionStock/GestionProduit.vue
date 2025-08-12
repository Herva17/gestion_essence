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
            Gestion Essence - Station KMJ
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
            class="q-mr-xs"
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
      :class="$q.dark.isActive ? 'bg-dark' : 'bg-grey-2'"
    >
      <!-- En-tête du drawer -->
      <div class="q-px-md q-py-md row items-center">
        <q-avatar size="md" class="bg-primary text-white q-mr-sm">📦</q-avatar>
        <q-toolbar-title class="text-weight-bold">
          Gestion Essence
        </q-toolbar-title>
      </div>
      
      <q-separator />

      <!-- Liste des menus -->
      <q-list padding class="menu-list">
        <q-item
          v-for="item in menuItems"
          :key="item.to"
          :to="item.to"
          exact
          clickable
          v-ripple
          :active="link === item.name"
          @click="link = item.name"
          :class="{ 'active-menu-item': link === item.name }"
        >
          <q-item-section avatar>
            <q-icon :name="item.icon" />
          </q-item-section>
          <q-item-section>{{ item.label }}</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- Contenu principal -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, watch } from 'vue'

const leftDrawerOpen = ref(false)
const miniState = ref(false)
const link = ref(localStorage.getItem('activeLinkProduit') || 'dashboard')

// Items du menu
const menuItems = [
  { name: 'dashboard', to: '/Ch/produit-page', icon: 'dashboard', label: 'Dashboard' },
  { name: 'produit', to: '/Ch/produit-form', icon: 'inventory_2', label: 'Approvisionnement' },
  { name: 'categorie', to: '/Ch/categorie-form', icon: 'category', label: 'Produits' },
  { name: 'mouvement', to: '/Ch/mouvement-form', icon: 'swap_horiz', label: 'Mouvement Stock' },
  { name: 'separator', to: '', icon: '', label: '' },
  { name: 'help', to: '/help', icon: 'help', label: 'Aide' }
]

// Sauvegarde l'état du menu actif
watch(link, (newValue) => {
  localStorage.setItem('activeLinkProduit', newValue)
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
</style>