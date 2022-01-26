<template>
  <div>
    <div class="border border-blue-500 bg-blue-200 rounded-b-lg p-2 mb-2">
      <!-- Controles para el ordenamiento -->
      <div class="mb-1">
        <p class="text-sm font-bold tracking-wide">Ordernar por:</p>
        <!-- Control Container -->
        <div class="flex justify-between">
          <div class="flex items-center text-sm">
            <input type="radio" name="oldFirst" id="oldFirst" value="oldFirst" class="mr-2" v-model="sortBy">
            <label for="oldFirst">Mas Antiguas </label>
          </div>

          <div class="flex items-center text-sm">
            <input type="radio" name="recentFirst" id="recentFirst" value="recentFirst" class="mr-2" v-model="sortBy">
            <label for="recentFirst">Mas recientes</label>
          </div>
        </div>

      </div>
      <!-- Controles para la busqueda -->
      <div>
        <input type="text" name="search" class="w-full py-1 px-3 text-sm rounded" placeholder="Buscas por contenido" v-model="search">
      </div>
    </div>
    <div class="max-h-screen overflow-scroll">
      <TransactionCard @updateTransaction="updateTransaction" v-for="item in sortedTransactions" :key="item.id" :transaction="item"/>
    </div>
  </div>
</template>
<script>

import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import TransactionCard from "@/Pages/Cashbox/Components/TransactionCard.vue";

export default {
  components: {
    JetButton,
    JetDangerButton,
    TransactionCard,
  },
  props:{
    transactions: {
      type: Array,
      default: [],
    }
  },
  emits:['updateTransaction'],
  data() {
    return {
      sortBy: 'recentFirst',
      search: "",
    }
  },
  methods: {
    updateTransaction(data){
      this.$emit('updateTransaction', data);
    }
  },
  computed: {
    sortedTransactions(){
      let filtered = this.transactions.filter(item => {
        //Se recupera la descripción y se normaliza
        let description = item.description.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        let search = this.search.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        
        return description.includes(search);
      })

      return filtered.sort((t1,t2) => {
        if(t1.date.isBefore(t2.date)){
          if(this.sortBy === 'recentFirst'){
            return 1;
          }

          return -1;
        }

        if(t1.date.isAfter(t2.date)){
          if(this.sortBy === 'recentFirst'){
            return -1;
          }

          return 1;
        }

        return 0;
      })
    }
  },
}
</script>