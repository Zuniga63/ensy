<template>
  <shell :show="show">
    <div class="relative w-full border-0 border-gray-800 border-opacity-80 bg-white overflow-hidden">
      <invoice-header :config="config" :invoice="invoice" class="mb-2" />

      <!-- Close Buttom -->
      <button
        class="
          absolute
          top-2
          right-2
          rounded-full
          bg-transparent
          hover:bg-white
          text-gray-600
          hover:text-gray-800 hover:shadow-sm
          transition-colors
          cursor-pointer
        "
        @click="$emit('closeInvoice')"
      >
        <div class="p-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
      </button>

      <div class="grid grid-cols-4 gap-4 px-4 py-2">
        <!-- Customer Information -->
        <div class="col-span-3">
          <customer-information :invoice="invoice" class="h-full" />
        </div>

        <!-- Dates -->
        <div class="p-2 border border-gray-200 rounded bg-gray-50 shadow">
          <!-- Expedition -->
          <div class="mb-2">
            <p class="text-gray-600 text-xs text-center mb-1">Fecha de Expedición</p>
            <p class="p-1 border border-gray-200 rounded bg-white text-sm text-center text-gray-800 shadow">
              {{ formatDate(invoice.expedition_date) }}
            </p>
          </div>
          <!-- Expiration -->
          <div>
            <p class="text-gray-600 text-xs text-center mb-1">Vencimiento</p>
            <p class="p-1 border border-gray-200 rounded bg-white text-sm text-center text-gray-800 shadow">
              {{ formatDate(invoice.expiration_date) }}
            </p>
          </div>
        </div>

        <!-- Tabs -->
        <div class="col-span-3">
          <custom-tab :tabs="tabs" :tabSelected="tab" @selectTab="tab = $event">
            <template #controls>
              <!-- Buttom for Payments -->
              <div v-show="enabledPayment" class="mr-4">
                <a
                  href="javascript:;"
                  class="
                    px-4
                    py-1
                    border border-blue-600
                    bg-blue-500
                    rounded-md
                    text-xs text-white
                    font-bold
                    tracking-wider
                    hover:opacity-80
                    transition
                    active:bg-blue-600
                  "
                  @click="$emit('showForm', 'add-payment')"
                  >Registrar Pago</a
                >
              </div>

              <!-- Buttom from Add Items -->
              <div v-if="false" class="mr-4">
                <a
                  href="javascript:;"
                  class="
                    px-4
                    py-1
                    border border-green-600
                    bg-green-500
                    rounded-md
                    text-xs text-white
                    font-bold
                    tracking-wider
                    hover:opacity-80
                    transition
                    active:bg-green-600
                  "
                  @click="$emit('showForm', 'add-item')"
                  >Agregar Item</a
                >
              </div>
            </template>

            <template #items>
              <item-list :items="invoice.items" @cancelItem="cancelItem" />
            </template>

            <template #pagos>
              <payment-list :payments="invoice.payments" @cancelPayment="cancelPayment" />
            </template>
          </custom-tab>
        </div>

        <!-- Sumary -->
        <div class="col-span-1">
          <invoice-sumary :sumary="sumary" />
        </div>
      </div>

      <footer class="flex justify-end px-4 py-3 bg-gray-300">
        <jet-danger-button class="mr-4" v-if="!invoice.cancel" @click="$emit('showCancelForm', { type: 'invoice' })"
          >Cancelar Factura
        </jet-danger-button>
        <a
          :href="route('invoice.print', invoice.id)"
          target="_blank"
          class="
            inline-flex
            items-center
            px-4
            py-2
            bg-gray-800
            border border-transparent
            rounded-md
            font-semibold
            text-xs text-white
            uppercase
            tracking-widest
            hover:bg-gray-700
            active:bg-gray-900
            focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300
            disabled:opacity-25
            transition
          "
        >
          Imprimir Factura
        </a>
      </footer>
    </div>
  </shell>
</template>

<script>
import Shell from "./Shell.vue";
import InvoiceHeader from "./InvocieHeader.vue";
import CustomerInformation from "./CustomerInformation.vue";
import dayjs from "dayjs";
import InvoiceSumary from "../NewInvoice/InvoiceSumary.vue";
import CustomTab from "@/Components/Tab.vue";
import ItemList from "./ItemList.vue";
import PaymentList from "./PaymentList.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";

export default {
  components: {
    Shell,
    InvoiceHeader,
    CustomerInformation,
    InvoiceSumary,
    CustomTab,
    ItemList,
    PaymentList,
    JetButton,
    JetDangerButton,
  },
  props: ["config", "invoice", "show"],
  emits: ["closeInvoice", "showForm", "showCancelForm"],
  data() {
    return {
      tabs: ["items", "pagos"],
      tab: "items",
      showingCancelPayment: false,
    };
  },
  methods: {
    formatDate(date) {
      const formatDate = "DD-MM-YYYY hh:mm a";
      return dayjs(date).format(formatDate);
    },
    cancelPayment(payment) {
      this.$emit("showCancelForm", {
        type: "payment",
        payment,
      });
    },
    cancelItem(item) {
      this.$emit("showCancelForm", {
        type: "item",
        item,
      });
    },
  }, //.end methods
  computed: {
    sumary() {
      let sumary = {
        subtotal: 0,
        discount: 0,
        total: 0,
        cash: 0,
        credit: 0,
        change: 0,
        balance: 0,
      };

      if (this.invoice) {
        sumary.subtotal = this.invoice.subtotal ? parseFloat(this.invoice.subtotal) : 0;
        sumary.discount = this.invoice.discount ? parseFloat(this.invoice.discount) : 0;
        sumary.total = parseFloat(this.invoice.amount);
        sumary.cash = this.invoice.cash ? parseFloat(this.invoice.cash) : 0;
        sumary.credit = this.invoice.credit ? parseFloat(this.invoice.credit) : 0;
        sumary.change = this.invoice.cash_change ? parseFloat(this.invoice.cash_change) : 0;
        sumary.balance = this.invoice.balance ? parseFloat(this.invoice.balance) : 0;
      }

      return sumary;
    },
    enabledPayment() {
      return this.invoice.customer_id && this.invoice.balance && this.tab === "pagos" && !this.invoice.cancel;
    },
  },
};
</script>
