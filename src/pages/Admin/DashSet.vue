<template>
  <div
    class="q-pa-md text-2xl mt-4 font-bold text-center mb-4 p-4 blondy text-white mx-2"
  >
    Parametres Comptes
  </div>
  <div class="container mx-auto p-4">
    <div class="q-pa-md">
      <div class="q-pa-sm">
        <AllLog />
      </div>

      <div class="q-pa-sm flex flex-col">
        <UserAdm />
      </div>
    </div>
  </div>
</template>
<script>
import AllLog from "components/Admin/AllLog.vue";
import UserAdm from "components/Admin/UserAdm.vue";

import { defineComponent } from "vue";

export default defineComponent({
  components: { AllLog, UserAdm },
});
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
