const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      { path: "", component: () => import("pages/IndexPage.vue") },

      // DASHBOARD ADMIN
      {
        path: "Main",
        component: () => import("pages/Admin/MyDash.vue"),
        children: [
          { path: "", redirect: "Dash" },
          { path: "Dash", component: () => import("pages/Admin/DashSet.vue") },
          { path: "Das", component: () => import("pages/Admin/DashSet.vue") },
          { path: "produit-page", component: () => import("pages/GestionStock/ProduitPage.vue") },
          { path: "produit-form", component: () => import("pages/GestionStock/ProduitForm.vue") },
          { path: "mouvement-form", component: () => import("pages/GestionStock/MouvementForm.vue") },
          { path: "categorie-form", component: () => import("pages/GestionStock/CategorieForm.vue") },
          { path: "client-form", component: () => import("pages/GestionCommande/ClientForm.vue") },
          { path: "commande-form", component: () => import("pages/GestionCommande/CommandeForm.vue") },
          { path: "commande-vente", component: () => import("pages/GestionCommande/VenteForm.vue") },
            {
        path: "rapport_fiche-stock",
        component: () => import("pages/Rapport/FicheStock.vue"), // Ajout de la route pour FacturePage
      },
        ],
      },

      // GESTION STOCK
      {
        path: "Ch",
        component: () => import("pages/GestionStock/GestionProduit.vue"),
        children: [
          { path: "", redirect: "produit-page" },
          { path: "produit-page", component: () => import("pages/GestionStock/ProduitPage.vue") },
          { path: "categorie-form", component: () => import("pages/GestionStock/CategorieForm.vue") },
          { path: "mouvement-form", component: () => import("pages/GestionStock/MouvementForm.vue") },
          { path: "produit-form", component: () => import("pages/GestionStock/ProduitForm.vue") },
        ],
      },

      // GESTION COMMANDE
      {
        path: "com",
        component: () => import("pages/GestionCommande/GestionComande.vue"), // <-- corriger ici selon le vrai nom du fichier
        children: [
          { path: "", redirect: "commande-dashboard" },
          { path: "commande-dashboard", component: () => import("pages/GestionCommande/CommandePage.vue") },
          { path: "client-form", component: () => import("pages/GestionCommande/ClientForm.vue") },
          { path: "mouvement-form", component: () => import("pages/GestionStock/MouvementForm.vue") },
          { path: "commande-form", component: () => import("pages/GestionCommande/CommandeForm.vue") },
           { path: "commande-vente", component: () => import("pages/GestionCommande/VenteForm.vue") },
        ],
      },

      // GESTION RAPPORT
      {
        path: "rapport",
        component: () => import("pages/Rapport/FicheStock.vue"), // Ajout de la route pour FacturePage
      },
          {
        path: "journal-vente",
        component: () => import("pages/Rapport/Fiche_journalierPage.vue"),
      },
    ],
  },


  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/ErrorNotFound.vue"),
  },
];

export default routes;
