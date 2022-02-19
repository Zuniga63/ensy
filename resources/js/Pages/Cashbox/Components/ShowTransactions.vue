<template>
  <div>
    <!-- Mobile Version -->
    <div class="lg:hidden">
      <div class="border border-blue-500 bg-blue-200 rounded-b-lg p-2 mb-2">
        <!-- Controles para el ordenamiento -->
        <div class="mb-1">
          <p class="text-sm font-bold tracking-wide">Ordernar por:</p>
          <!-- Control Container -->
          <div class="flex justify-between">
            <div class="flex items-center text-sm">
              <input
                type="radio"
                name="oldFirst"
                id="oldFirst"
                value="oldFirst"
                class="mr-2"
                v-model="sortBy"
              />
              <label for="oldFirst">Mas Antiguas </label>
            </div>

            <div class="flex items-center text-sm">
              <input
                type="radio"
                name="recentFirst"
                id="recentFirst"
                value="recentFirst"
                class="mr-2"
                v-model="sortBy"
              />
              <label for="recentFirst">Mas recientes</label>
            </div>
          </div>
        </div>
        <!-- Controles para la busqueda -->
        <div>
          <input
            type="text"
            name="search"
            class="w-full py-1 px-3 text-sm rounded"
            placeholder="Buscas por contenido"
            v-model="search"
          />
        </div>
      </div>
      <div class="max-h-screen overflow-scroll">
        <TransactionCard
          @updateTransaction="updateTransaction"
          v-for="item in sortedTransactions"
          :key="item.id"
          :transaction="item"
        />
      </div>
    </div>

    <!-- desktop version -->
    <div class="hidden lg:block">
      <!-- Controles -->
      <div class="flex justify-between mb-4">
        <!-- Controles de ordenamiento -->
        <div>
          <label for="desktopOrderBy" class="inline-block mr-2"
            >Ordenar transacciones por:</label
          >
          <select
            name="desktopOrderBY"
            id="desktopOrderBy"
            class="
              w-80
              px-4
              py-2
              border-gray-300
              focus:border-indigo-300
              rounded-md
              focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            "
            v-model="sortBy"
          >
            <option value="recentFirst">Mas recientes primero</option>
            <option value="oldFirst">Más Antiguas primero</option>
          </select>
        </div>

        <!-- Control de busqueda -->
        <div>
          <input
            type="text"
            name="searchByDescription"
            placeholder="Buscar por su descripción."
            v-model="search"
            class="
              w-80
              px-4
              py-2
              border-gray-300
              focus:border-indigo-300
              rounded-md
              focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            "
          />
          <!-- TODO: Input con el buscador -->
        </div>
      </div>

      <!-- Tabla -->
      <div class="h-[28rem] shadow border-b border-gray-300 overflow-y-scroll">
        <table class="relative min-w-full table-auto">
          <thead class="sticky top-0 bg-gray-50">
            <tr>
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  text-center text-gray-500
                  tracking-wider
                  uppercase
                "
              >
                ID
              </th>
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  tetx-left
                  text-gray-500
                  tracking-wider
                  uppercase
                "
              >
                Fecha
              </th>
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  tetx-left
                  text-gray-500
                  tracking-wider
                  uppercase
                "
              >
                Descripción
              </th>
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  tetx-left
                  text-gray-500
                  tracking-wider
                  uppercase
                "
              >
                Importe
              </th>
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  tetx-left
                  text-gray-500
                  tracking-wider
                  uppercase
                "
              >
                Saldo
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <transaction-row
              v-for="item in sortedTransactions"
              :key="item.id"
              :transaction="item"
              @updateTransaction="updateTransaction"
            />
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import TransactionCard from "@/Pages/Cashbox/Components/TransactionCard.vue";
import TransactionRow from "@/Pages/Cashbox/Components/TransactionRow.vue";

export default {
  components: {
    JetButton,
    JetDangerButton,
    TransactionCard,
    TransactionRow,
  },
  props: {
    transactions: {
      type: Array,
      default: [],
    },
  },
  emits: ["updateTransaction"],
  data() {
    return {
      sortBy: "recentFirst",
      search: "",
    };
  },
  methods: {
    updateTransaction(data) {
      this.$emit("updateTransaction", data);
    },
  },
  computed: {
    sortedTransactions() {
      let filtered = this.transactions.filter((item) => {
        //Se recupera la descripción y se normaliza
        let description = item.description
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "");
        let search = this.search
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "");

        return description.includes(search);
      });

      return filtered.sort((t1, t2) => {
        let reverse = this.sortBy === "recentFirst" ? true : false;

        if (t1.date.isBefore(t2.date)) {
          if (reverse) {
            return 1;
          }

          return -1;
        }

        if (t1.date.isAfter(t2.date)) {
          if (reverse) {
            return -1;
          }

          return 1;
        }

        if (t1.date.isSame(t2.date)) {
          if (t1.createdAt.isBefore(t2.createdAt)) {
            if (reverse) {
              return 1;
            }

            return -1;
          }

          if (t1.createdAt.isAfter(t2.createdAt)) {
            if (reverse) {
              return -1;
            }

            return 1;
          }

          return 0;
        }

        return 0;
      });
    },
    windowHeight() {
      return window.innerHeight;
    },
  },
};
</script>