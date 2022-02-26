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
      <div class="max-h-screen overflow-scroll" v-if="window.width < 1024">
        <TransactionCard
          @updateTransaction="updateTransaction"
          v-for="item in pages[page]?.transactions"
          :key="item.id"
          :transaction="item"
        />
      </div>
    </div>

    <!-- desktop version -->
    <div class="hidden lg:block" v-if="window.width >= 1024">
      <!-- Controles -->
      <div class="flex justify-between items-center mb-4">
        <!-- Controles de ordenamiento -->
        <div class="flex flex-col">
          <label for="desktopOrderBy" class="inline-block mb-2"
            >Ordenar por:</label
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
              text-sm text-gray-800
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
          <div class="flex flex-col">
            <label for="desktopSearch" class="inline-block mb-2 text-gray-800"
              >Filtrar por descripción</label
            >
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
                text-sm text-gray-800
                focus:ring focus:ring-indigo-200 focus:ring-opacity-50
              "
            />
          </div>
          <!-- TODO: Input con el buscador -->
        </div>
      </div>

      <!-- Tabla -->
      <div
        class="h-[28rem] shadow border-b border-gray-300 overflow-y-scroll mb-4"
      >
        <table class="relative min-w-full table-auto mb-2">
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
          <tbody class="bg-white divide-y divide-gray-200" v-if="pages.length">
            <transaction-row
              v-for="item in pages[page]?.transactions"
              :key="item.id"
              :transaction="item"
              @updateTransaction="updateTransaction"
              ref="tran"
            />
          </tbody>
        </table>
      </div>

      <div class="flex items-center">
        <div class="flex items-center mr-4">
          <p class="text-sm text-gray-800 mr-2">Pag.:</p>

          <select
            name="page"
            id="page"
            v-model="page"
            class="border border-gray-200 rounded mr-2 text-sm"
          >
            <option v-for="(item, index) in pages" :key="index" :value="index">
              {{ item.page }}
            </option>
          </select>

          <div class="self-stretch mr-2 border border-gray-200"></div>

          <p class="text-sm text-gray-800 mr-2">Filas:</p>

          <select
            name="items"
            id="items"
            v-model="itemsPerPage"
            class="border border-gray-200 rounded mr-2 text-sm"
          >
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="75">75</option>
            <option value="100">100</option>
            <option value="500">500</option>
            <option value="1000">1000</option>
          </select>
        </div>

        <p class="text-gray-400">
          Mostrando pag.
          <span class="font-bold">{{ page + 1 }}</span>
          de {{ pages.length }} | Transacciones:
          {{ sortedTransactions.length }} | Sumatoria:
          {{ formatCurrency(balance) }}
        </p>
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
  setup(props) {
    //---------------------------------------------------------
    // SE CONSTRUYE EL FORMATEADOR DE MONEDA
    //---------------------------------------------------------
    let fractionDigits = 0;
    let style = "currency";
    let currency = "COP";

    let formater = new Intl.NumberFormat("es-CO", {
      style,
      currency,
      minimumFractionDigits: fractionDigits,
    });

    return { formater };
  },
  data() {
    return {
      /**
       * Permite establecer el orden de ordenación de las transacciones
       * tiene dos valores posibles recentFirst y oldFirst
       * @type {string}
       */
      sortBy: "recentFirst",
      /**
       * Permite filtrar las transacicones por coincidencias
       * en la propiedad descripción.
       * @type {string}
       */
      search: "",
      /**
       * Indica la pagina de transacciones que se estpa visualizando
       * @type {number}
       */
      page: 0,
      /**
       * Establece el numero de transacciónes que se visualizan por pag
       * @type {number}
       */
      itemsPerPage: 25,
      /**
       * Establece las dimensiones de la ventana
       * y definir que se muestra y que no.
       * @type {object}
       */
      window: {
        width: 0,
        height: 0,
      },
    };
  },
  methods: {
    /**
     * Emite un evento para activar el formulario de actualización
     * enviando los datos de la transacción.
     * @param {object} data Instancia de la transacción a actualizar.
     */
    updateTransaction(data) {
      this.$emit("updateTransaction", data);
    },
    /**
     * Actualiza los parametros de la ventana cada que
     * esta se redimensiona.
     */
    handleResize() {
      this.window.width = window.innerWidth;
      this.window.height = window.innerHeight;
    },
    /**
     * Se encarga normalizar el texto y sirva para
     * realizar busqudas.
     * @param {string} text texto a normlaizar
     * @returns {string}
     */
    nomalizeText(text) {
      return text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
    },
    formatCurrency(number) {
      return this.formater.format(number);
    },
  },
  computed: {
    sortedTransactions() {
      let filtered = this.transactions.filter((item) => {
        //Se recupera la descripción y se normaliza
        let description = this.nomalizeText(item.description);
        let search = this.nomalizeText(this.search);

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
    pages() {
      let page = 1;
      let transactions = [];
      let pages = [];

      this.sortedTransactions.forEach((item) => {
        if (transactions.length < this.itemsPerPage) {
          transactions.push(item);
        } else {
          pages.push({
            page,
            transactions,
          });

          page++;
          transactions = [item];
        }
      });

      pages.push({
        page,
        transactions,
      });

      return pages;
    },
    balance() {
      let allAmounts = this.sortedTransactions.map((item) => item.amount);
      return allAmounts.length ? allAmounts.reduce((prev, next) => prev + next) : 0;
    },
  },
  watch: {
    pages(newPages, oldPages) {
      if (newPages.length < this.page + 1) {
        console.log("Pages cambio", newPages.length);
        this.page = newPages.length - 1;
      }
    },
  },
  created() {
    window.addEventListener("resize", this.handleResize);
    this.handleResize();
  },
  destroyed() {
    window.removeEventListener("resize", this.handleResize);
  },
};
</script>