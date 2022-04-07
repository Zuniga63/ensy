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
            text-xs
          "
          placeholder="Documento o numero de factura."
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
      <div class="grid grid-cols-2 gap-2 mb-2">
        <!-- From -->
        <div class="flex flex-col">
          <label
            for="fromDate"
            class="text-xs text-gray-800 mb-1 tracking-wider"
            >Desde</label
          >
          <input
            type="date"
            name="fromDate"
            id="fromDate"
            class="
              border border-gray-200
              rounded
              focus:ring focus:ring-indigo-400 focus:ring-opacity-50
              text-xs
            "
          />
        </div>

        <!-- To -->
        <div class="flex flex-col">
          <label for="toDate" class="text-xs text-gray-800 mb-1 tracking-wider"
            >Hasta</label
          >
          <input
            type="date"
            name="toDate"
            id="toDate"
            class="
              border border-gray-200
              rounded
              focus:ring focus:ring-indigo-400 focus:ring-opacity-50
              text-xs
            "
          />
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
            focus:outline-none
            focus:ring
            focus:ring-emerald-500
            focus:ring-opacity-40
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
            border border-gray-800
            rounded-md
            overflow-hidden
            hover:cursor-pointer
            hover:ring
            hover:ring-gray-500
            hover:ring-opacity-40
            transition-shadow
            shadow
            mb-4
          "
          v-for="invoice in invoiceList"
          :key="invoice.id"
          @click.stop="$emit('loadInvoice', invoice.id)"
        >
          <header class="flex justify-between px-2 py-2 bg-gray-800">
            <p class="text-xs text-gray-50">
              Factura N°: <span class="font-medium">{{ invoice.number }}</span>
            </p>
            <p class="text-xs text-gray-50 font-medium">
              {{ invoice.time.expeditionDate }}
            </p>
          </header>
          <div class="p-2">
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
                <p
                  class="text-center tex-sm text-gray-800 tracking-wider font-"
                >
                  {{ formatCurrency(invoice.amount) }}
                </p>
              </div>

              <!-- Balance -->
              <div v-if="invoice.balance">
                <h3 class="text-xs text-gray-400 text-center">Saldo</h3>
                <p
                  class="text-center tex-sm text-gray-800 tracking-wider font-"
                >
                  {{ formatCurrency(invoice.balance) }}
                </p>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <footer class="px-4 py-2 bg-gray-200">
      <p class="text-gray-800 text-center text-sm">
        Facturas: <span class="font-medium"> {{ invoiceList.length }} </span>
      </p>
    </footer>
  </div>
</template>

<script>
import SearchIcon from "@/Components/Svgs/Search.vue";
import { formatCurrency } from "@/utilities.js";
import dayjs from "dayjs";
import isToday from "dayjs/plugin/isToday";

export default {
  components: {
    SearchIcon,
  },
  emits: ["enabledForm", 'loadInvoice'],
  props: {
    invoices: {
      type: Array,
      default: [],
    },
  },
  setup(props) {
    dayjs.extend(isToday);
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
        expiration: dayjs(invoice.expiration_date),
        createAt: dayjs(invoice.created_at),
        updateAt: dayjs(invoice.updated_at),
      };

      invoice.time.expeditionDate = invoice.time.expedition.isToday()
        ? invoice.time.expedition.format("hh:mm a")
        : invoice.time.expedition.format("DD-MM-YY");
    },
  }, //.end methods
  computed: {
    invoiceList() {
      let list = this.invoices.slice();
      list.map((item) => this.createTimeProperty(item));
      list.sort((inv1, inv2) => {
        if (inv1.time.createAt.isBefore(inv2.time.createdAt)) return -1;
        if (inv1.time.createAt.isAfter(inv2.time.createdAt)) return 1;

        return 0;
      });
      return list;
    },
  },
};
</script>