<template>
  <div class="rounded overflow-hidden shadow bg-white">
    <header class="px-4 py-2 bg-gray-200">
      <!-- Buscador por documento o Numero de factura -->
      <div class="flex items-center w-full mb-2">
        <input
          type="text"
          name="search"
          id="search"
          class="
            flex-grow
            w-full
            border border-gray-200
            rounded
            mr-2
            focus:ring focus:ring-indigo-400 focus:ring-opacity-50
            text-xs text-gray-800
          "
          placeholder="Documento o numero de factura."
          v-model="search"
        />

        <button
          class="
            rounded-full
            p-2
            text-gray-800
            hover:bg-white
            focus:bg-white focus:border-none focus:outline-none
            transition-colors
          "
        >
          <search-icon class="h-5 w-5" />
        </button>
      </div>

      <!-- Controles para filtrar por fecha -->
      <div class="mb-2">
        <label class="flex items-center mb-2 text-xs text-gray-800">
          <jet-checkbox v-model:checked="showDateInputs" class="mr-2" />
          <span>Filtrar por fechas</span>
        </label>

        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-90"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-90"
        >
          <div class="grid grid-cols-2 gap-2" v-show="showDateInputs">
            <!-- From -->
            <div class="flex flex-col">
              <label for="fromDate" class="text-xs text-gray-800 mb-1 tracking-wider">Desde</label>
              <input
                type="date"
                name="fromDate"
                id="fromDate"
                class="
                  border border-gray-200
                  rounded
                  focus:ring focus:ring-indigo-400 focus:ring-opacity-50
                  text-xs text-gray-800
                "
                v-model="fromDate"
                :max="maxDate"
              />
            </div>

            <!-- To -->
            <div class="flex flex-col">
              <label for="toDate" class="text-xs text-gray-800 mb-1 tracking-wider">Hasta</label>
              <input
                type="date"
                name="toDate"
                id="toDate"
                class="
                  border border-gray-200
                  rounded
                  focus:ring focus:ring-indigo-400 focus:ring-opacity-50
                  text-xs text-gray-800
                "
                v-model="toDate"
                :min="fromDate"
                :max="maxDate"
              />
            </div>
          </div>
        </transition>
      </div>

      <!-- Controles para filtros generales -->
      <div class="mb-2">
        <h2 class="mb-1 text-sm text-gray-800">Mostrar Facturas</h2>
        <div class="grid grid-cols-2 gap-2">
          <label class="flex items-center text-xs text-gray-800">
            <input
              type="radio"
              name="all"
              class="mr-2 text-indigo-600 focus:ring-indigo-200"
              v-model="filterBy"
              value="all"
            />
            <span>Todas</span>
          </label>

          <label class="flex items-center text-xs text-gray-800">
            <input
              type="radio"
              name="pay"
              class="mr-2 text-indigo-600 focus:ring-indigo-200"
              v-model="filterBy"
              value="paid"
            />
            <span>Pagadas</span>
          </label>

          <label class="flex items-center text-xs text-gray-800">
            <input
              type="radio"
              name="pending"
              class="mr-2 text-indigo-600 focus:ring-indigo-200"
              v-model="filterBy"
              value="pending"
            />
            <span>Pendientes</span>
          </label>

          <label class="flex items-center text-xs text-gray-800">
            <input
              type="radio"
              name="canceled"
              class="mr-2 text-indigo-600 focus:ring-indigo-200"
              v-model="filterBy"
              value="canceled"
            />
            <span>Anuladas</span>
          </label>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <button
          class="
            col-span-2
            px-4
            py-2
            border border-emerald-700
            shadow
            rounded
            bg-emerald-500
            text-white text-xs
            font-semibold
            hover:ring hover:ring-emerald-500 hover:ring-opacity-40
            focus:outline-none focus:ring focus:ring-emerald-500 focus:ring-opacity-40
            transition-shadow
          "
          @click="$emit('enabledForm', 'new-invoice')"
        >
          Nueva Factura
        </button>
      </div>
    </header>

    <!-- Body -->
    <div class="h-80 px-4 py-2 overflow-y-auto">
      <ul>
        <li
          class="
            border
            rounded-md
            overflow-hidden
            hover:cursor-pointer hover:ring hover:ring-gray-500 hover:ring-opacity-40
            transition-shadow
            shadow
            mb-4
          "
          v-for="invoice in displayList"
          :key="invoice.id"
          @click.stop="$emit('loadInvoice', invoice.id)"
          :class="{
            'opacity-40': invoice.cancel,
            'border-gray-800 hover:ring-gray-500': invoice.balance,
            'border-emerald-600 hover:ring-emerald-500': !invoice.balance,
          }"
        >
          <header
            class="flex justify-between px-2 py-2 bg-gray-800"
            :class="{ 'bg-gray-800': invoice.balance, 'bg-emerald-600': !invoice.balance }"
          >
            <p class="text-xs text-gray-50">
              Factura N°: <span class="font-medium">{{ invoice.invoice_number }}</span>
            </p>
            <p class="text-xs text-gray-50 font-medium">
              {{ invoice.time.expeditionDate }}
            </p>
          </header>
          <div class="relative p-2">
            <!-- Customer -->
            <p class="text-xs text-gray-700 line-clamp-1">
              Cliente:
              <span class="font-semibold italic">
                {{ invoice.customer_name }}
              </span>
            </p>
            <!-- Vendedor -->
            <p class="text-xs text-gray-700 line-clamp-1">
              Vendedor:
              <span class="font-semibold italic">
                {{ invoice.seller_name }}
              </span>
            </p>

            <!-- Importe y Saldo -->
            <div
              class="flex mt-2"
              :class="{
                'justify-center': !invoice.balance,
                'justify-around': invoice.balance,
              }"
            >
              <!-- Amount -->
              <div class="">
                <h3 class="text-xs text-gray-400 text-center">Valor Factura</h3>
                <p class="text-center tex-sm text-gray-800 tracking-wider font-">
                  {{ formatCurrency(invoice.amount) }}
                </p>
              </div>

              <!-- Balance -->
              <div v-if="invoice.balance">
                <h3 class="text-xs text-gray-400 text-center">Saldo</h3>
                <p class="text-center tex-sm text-gray-800 tracking-wider font-">
                  {{ formatCurrency(invoice.balance) }}
                </p>
              </div>
            </div>

            <p class="text-xs text-red-800 line-clamp-1 text-center" v-if="invoice.cancel">
              *{{ invoice.cancel_message }}*
            </p>

            <!-- Eye -->
            <star-icon
              class="absolute top-2 right-2 h-5 w-5 text-yellow-500"
              solid
              v-if="selectedInvoice?.id === invoice.id"
            />
          </div>
        </li>
      </ul>

      <button
        class="
          w-full
          px-4
          py-2
          border border-indigo-600
          rounded
          mt-2
          text-xs
          tracking-wider
          uppercase
          text-indigo-800
          font-semibold
          hover:bg-indigo-500 hover:text-white
          active:bg-indigo-600
          transition-colors
        "
        @click="invoiceDisplayed += step"
        v-if="displayList.length < filterList.length"
      >
        Cargar mas Facturas
      </button>
    </div>

    <footer class="px-4 py-2 bg-gray-200">
      <p class="text-gray-800 text-center text-sm">
        Mostrando <span class="font-semibold">{{ displayList.length }}</span> facturas de
        <span class="font-semibold">{{ filterList.length }}</span>
      </p>
    </footer>
  </div>
</template>

<script>
import SearchIcon from "@/Components/Svgs/Search.vue";
import StarIcon from "@/Components/Svgs/Star.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import { formatCurrency, normalizeString } from "@/utilities.js";
import dayjs from "dayjs";
import isToday from "dayjs/plugin/isToday";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import isSameOrBefore from "dayjs/plugin/isSameOrBefore";

export default {
  components: {
    SearchIcon,
    JetCheckbox,
    StarIcon,
  },
  emits: ["enabledForm", "loadInvoice"],
  props: {
    invoices: {
      type: Array,
      default: [],
    },
    selectedInvoice: {
      type: Object,
      default: null,
    },
  },
  setup(props) {
    dayjs.extend(isToday);
    dayjs.extend(isSameOrAfter);
    dayjs.extend(isSameOrBefore);
  },
  data() {
    return {
      search: null,
      /**
       * Define que facturas se muestran
       * [all, paid, pending, canceled]
       */
      filterBy: "all",
      showDateInputs: false,
      fromDate: null,
      toDate: null,
      maxDate: dayjs().format("YYYY-MM-DD"),
      invoiceDisplayed: 10,
      step: 10,
    };
  },
  methods: {
    formatCurrency,
    /**
     * Metodo encargado en convertir las fechas en unidades de tiempo
     * @param {object} invoice instancia de la factura proveniente de la base de datos
     */
    createTimeProperty(invoice) {
      invoice.time = {
        expedition: dayjs(invoice.expedition_date),
        /* expiration: dayjs(invoice.expiration_date),
        createAt: dayjs(invoice.created_at),
        updateAt: dayjs(invoice.updated_at), */
      };

      invoice.time.expeditionDate = invoice.time.expedition.isToday()
        ? invoice.time.expedition.format("hh:mm a")
        : invoice.time.expedition.format("DD-MM-YY");
    },
    /**
     * Se encarga de filtrar las facturas segun el texto
     * de busqueda.
     * @param {array} list - listado de facturas
     * @return {array}
     */
    filterBySearch(list) {
      let search = normalizeString(this.search.trim());
      return list.filter((item) => {
        let text = `${item.invoice_number} ${item.customer_name} ${item.customer_document}`;
        text = normalizeString(text);
        return text.includes(search);
      });
    },
    /**
     * Se encarga de filtrar las facturas segun si están pagadas,
     * pendientes o anuladas.
     * @param {array} list listado de facturas.
     * @return {array}
     */
    filterByGeneral(list) {
      if (this.filterBy === "all") return list;
      if (this.filterBy === "paid") return list.filter((item) => !item.balance && !item.cancel);
      if (this.filterBy === "pending") return list.filter((item) => item.balance && !item.cancel);
      if (this.filterBy === "canceled") return list.filter((item) => item.cancel);

      return [];
    },
    /**
     * Se encarga de filtrar las facturas segun las fechas seleccionadas.
     * @param {array} list listado de facturas.
     * @return {array}
     */
    filterByDate(list) {
      const from = this.fromDate ? dayjs(this.fromDate).startOf("day") : null;
      const to = this.toDate ? dayjs(this.toDate).endOf("day") : null;

      if (from && to && from.isValid() && to.isValid()) {
        return list.filter((item) => {
          return item.time.expedition.isSameOrAfter(from) && item.time.expedition.isSameOrBefore(to);
        });
      } else if (from && from.isValid()) {
        return list.filter((item) => {
          return item.time.expedition.isSameOrAfter(from);
        });
      } else if (to && to.isValid()) {
        return list.filter((item) => {
          return item.time.expedition.isSameOrBefore(to);
        });
      }

      return list;
    },
  }, //.end methods
  computed: {
    filterList() {
      let list = this.filterByGeneral(this.invoiceList.reverse());
      if (this.showDateInputs) list = this.filterByDate(list);
      if (this.search) list = this.filterBySearch(list);
      return list;
    },
    displayList() {
      return this.filterList.slice(0, this.invoiceDisplayed);
    },
    /**
     * @return {Array[object]}
     */
    invoiceList() {
      let list = this.invoices.slice();
      list.map((item) => this.createTimeProperty(item));
      return list;
    },
  },
  watch: {
    fromDate(newDate, oldDate) {
      if (newDate !== oldDate) {
        if (this.toDate) {
          let from = dayjs(newDate);
          let to = dayjs(this.toDate);

          if (from.isAfter(to)) {
            this.toDate = from.format("YYYY-MM-DD");
          }
        }
      }
    },
  },
  /* beforeMount() {
    //
  }, */
};
</script>