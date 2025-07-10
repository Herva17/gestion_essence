<template>
  <div class="facture-page">
    <!-- Section du logo -->
    <div class="logo-section">
      <img :src="logoUrl" alt="Logo Station KMJ" class="logo" />
    </div>

    <!-- Titre de la facture -->
    <h1 class="facture-title">Facture Client</h1>

    <!-- Informations du client -->
    <div class="client-info">
      <h2>Informations du Client</h2>
      <p><strong>Nom:</strong> {{ client.name }}</p>
      <p><strong>Adresse:</strong> {{ client.address }}</p>
      <p><strong>Téléphone:</strong> {{ client.phone }}</p>
    </div>

    <!-- Détails de la facture -->
    <div class="facture-details">
      <h2>Détails de la Facture</h2>
      <table class="facture-table">
        <thead>
          <tr>
            <th>Article</th>
            <th>Quantité</th>
            <th>Prix Unitaire ($)</th>
            <th>Total ($)</th>
            <th>Prix Unitaire (FC)</th>
            <th>Total (FC)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in factureItems" :key="item.id">
            <td>{{ item.name }}</td>
            <td>{{ item.quantity }}</td>
            <td>{{ item.unitPrice }} $</td>
            <td>{{ item.total }} $</td>
            <td>{{ item.unitPrice * exchangeRate }} FC</td>
            <td>{{ item.total * exchangeRate }} FC</td>
          </tr>
        </tbody>
      </table>
      <div class="facture-total">
        <p><strong>Total Général:</strong> {{ totalGeneral }} $</p>
        <p><strong>Total Général (FC):</strong> {{ totalGeneral * exchangeRate }} FC</p>
      </div>
    </div>

    <!-- Bouton Imprimer -->
    <div class="print-button-section">
      <button @click="printFacture" class="print-button">Imprimer la Facture</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "FacturePage",
  data() {
    return {
      logoUrl: "path/to/logo.png", // Remplacez par le chemin réel du logo
      client: {
        name: "Jean Dupont",
        address: "123 Rue Exemple, Ville",
        phone: "0123456789",
      },
      factureItems: [
        { id: 1, name: "Produit A", quantity: 2, unitPrice: 50, total: 100 },
        { id: 2, name: "Produit B", quantity: 1, unitPrice: 30, total: 30 },
      ],
      exchangeRate: 3000, // Taux de conversion : 1 dollar = 3000 FC
    };
  },
  computed: {
    totalGeneral() {
      return this.factureItems.reduce((sum, item) => sum + item.total, 0);
    },
  },
  methods: {
    printFacture() {
      window.print();
    },
  },
};
</script>

<style scoped>
.facture-page {
  font-family: Arial, sans-serif;
  padding: 20px;
  max-width: 800px;
  margin: auto;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.logo-section {
  text-align: center;
  margin-bottom: 20px;
}

.logo {
  max-width: 150px;
  height: auto;
}

.facture-title {
  text-align: center;
  font-size: 24px;
  margin-bottom: 20px;
}

.client-info h2,
.facture-details h2 {
  font-size: 18px;
  margin-bottom: 10px;
}

.facture-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.facture-table th,
.facture-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

.facture-table th {
  background-color: #f2f2f2;
}

.facture-total {
  text-align: right;
  font-size: 16px;
}

.print-button-section {
  text-align: center;
  margin-top: 20px;
}

.print-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  border-radius: 5px;
}

.print-button:hover {
  background-color: #45a049;
}

/* Styles spécifiques à l'impression */
@media print {
  .print-button-section {
    display: none; /* Cache le bouton Imprimer */
  }

  /* Cache le texte "Quasar App" si présent */
  body::before {
    content: none;
  }
}
</style>
