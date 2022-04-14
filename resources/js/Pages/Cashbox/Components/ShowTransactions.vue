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
              <input type="radio" name="oldFirst" id="oldFirst" value="oldFirst" class="mr-2" v-model="sortBy" />
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
          @delete-transaction="deleteTransaction"
          v-for="item in pages[page]?.transactions"
          :key="item.id"
          :transaction="item"
        />
      </div>
    </div>

    <!-- desktop version -->
    <div class="hidden lg:block pt-2" v-if="window.width >= 1024">
      <!-- Controles -->
      <div class="grid grid-cols-4 gap-4 mb-4">
        <!-- Controles de ordenamiento -->
        <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
          <label
            for="desktopOrderBy"
            class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
            >Ordenar por:</label
          >
          <select
            name="desktopOrderBY"
            id="desktopOrderBy"
            class="
              w-full
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

        <!-- Desde -->
        <div>
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <label
              for="fromDate"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
              >Filtrar desde</label
            >
            <input
              type="date"
              id="fromDate"
              placeholder="Selecciona una fecha"
              v-model.lazy="fromDate"
              class="
                w-full
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

        <!-- Hasta -->
        <div>
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <label
              for="toDate"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
              >Filtrar hasta</label
            >
            <input
              type="date"
              id="toDate"
              v-model.lazy="toDate"
              :max="maxDate"
              class="
                w-full
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

        <!-- Control de busqueda -->
        <div>
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <label
              for="desktopSearch"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
              >Filtrar por descripción</label
            >
            <input
              type="text"
              id="desktopSearch"
              name="desktopSearch"
              placeholder="Buscar por su descripción."
              v-model.lazy.trim="search"
              class="
                w-full
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
      <div class="h-[28rem] shadow border-b border-gray-300 overflow-y-scroll mb-4">
        <table class="relative min-w-full table-auto mb-2">
          <thead class="sticky top-0 bg-gray-50">
            <tr>
              <th scope="col" class="px-4 py-3 text-center text-gray-500 tracking-wider uppercase">ID</th>
              <th scope="col" class="px-4 py-3 tetx-left text-gray-500 tracking-wider uppercase">Fecha</th>
              <th scope="col" class="px-4 py-3 tetx-left text-gray-500 tracking-wider uppercase">Descripción</th>
              <th scope="col" class="px-4 py-3 tetx-left text-gray-500 tracking-wider uppercase">Importe</th>
              <th scope="col" class="px-4 py-3 tetx-left text-gray-500 tracking-wider uppercase">Saldo</th>
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
              @delete-transaction="deleteTransaction"
              ref="tran"
            />
          </tbody>
        </table>
      </div>

      <div class="flex items-center">
        <div class="flex items-center mr-4">
          <p class="text-sm text-gray-800 mr-2">Pag.:</p>

          <select name="page" id="page" v-model="page" class="border border-gray-200 rounded mr-2 text-sm">
            <option v-for="(item, index) in pages" :key="index" :value="index">
              {{ item.page }}
            </option>
          </select>

          <div class="self-stretch mr-2 border border-gray-200"></div>

          <p class="text-sm text-gray-800 mr-2">Filas:</p>

          <select name="items" id="items" v-model="itemsPerPage" class="border border-gray-200 rounded mr-2 text-sm">
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
          de {{ pages.length }} | Transacciones: {{ sortedTransactions.length }} | Sumatoria:
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
import dayjs from "dayjs";
import isBetween from "dayjs/plugin/isBetween";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import isSameOrBefore from "dayjs/plugin/isSameOrBefore";
import { formatCurrency, normalizeString } from "@/utilities";

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
  emits: ["updateTransaction", "deleteTransaction"],
  setup(props) {
    dayjs.extend(isBetween);
    dayjs.extend(isSameOrAfter);
    dayjs.extend(isSameOrBefore);
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
       * Establece la fecha en la que se van empezar a filtar las
       * transacciones.
       */
      fromDate: dayjs().startOf('month').format("YYYY-MM-DD"),
      /**
       * Establece la fecha hasta la cual se van a filtrar las
       * transacciones.
       */
      toDate: dayjs().format("YYYY-MM-DD"),
      maxDate: dayjs().format("YYYY-MM-DD"),
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
    deleteTransaction(data) {
      this.$emit("deleteTransaction", data);
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
     * @return {string}
     */
    nomalizeText: normalizeString,
    formatCurrency,
    /**
     * Se encarga de recuperar las transacciones que
     * presentan coincidencia con el texto pasado como
     * parametro.
     * @param {string} text Textoque sirve para comparar
     * @param {array} list listado de transacciónes a filtrar.
     */
    filterByDescription(text, list) {
      text = this.nomalizeText(text);
      return list.filter((item) => {
        /**
         * @type {string}
         */
        let description = this.nomalizeText(item.description);
        return description.includes(text);
      });
    },
    /**
     * Ordena las transacciones de mas recientes o mas antiguas
     * segun el control de usuario.
     * @param {array} list listado de transacciones
     * @return {array}
     */
    sortByDate(list) {
      return list.sort((t1, t2) => {
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
    /**
     * Filtra las transacciones segun la fecha
     * @param {dayjs} fromDate Fecha desde la cual se empieza a filtrar
     * @param {dayjs} toDate fecha hasta la cual se filtra.
     * @param {array} list listado de transacciones a filtrar.
     */
    filterBetweenDates(fromDate, toDate, list) {
      return list.filter((item) => {
        let date = dayjs(item.date);

        if (fromDate && toDate) {
          return date.isBetween(fromDate, toDate);
        }

        if (fromDate) {
          return date.isSameOrAfter(fromDate);
        }

        if (toDate) {
          return date.isSameOrBefore(toDate);
        }

        return true;
      });
    },
  },
  computed: {
    sortedTransactions() {
      let list = this.transactions.slice();
      if (this.search) list = this.filterByDescription(this.search, list);

      if (this.fromDate || this.toDate) {
        let from = this.fromDate ? dayjs(this.fromDate) : null;
        let to = this.toDate ? dayjs(this.toDate).endOf("day") : null;
        list = this.filterBetweenDates(from, to, list);
      }

      return this.sortByDate(list);
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