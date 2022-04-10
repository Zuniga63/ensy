<template>
  <div>
    <header class="px-4 py-6 bg-indigo-50">
      <h2 class="mb-2 text-gray-800 text-center uppercase font-semibold">Formulario para el registro de Abonos</h2>
      <p class="text-center text-xs text-gray-600">
        Se va a realizar un abono a la factura Nº
        <span class="text-red-500 font-bold">{{ invoice.invoice_number }}</span> a nombre del cliente
        <span class="font-semibold italic"> {{ invoice.customer_name }} </span> cuyo saldo a la fecha es de
        <span class="font-bold">{{ formatCurrency(invoice.balance) }}</span>
      </p>
    </header>
    <div class="px-4 py-6">
      <cash-payment :boxs="boxs" @addPayment="addPayment" />
      <payment-list :payments="payments" @updateOrder="sortPayemntList" @removePayment="removePayment" />
      <div class="mt-2" v-if="error">
        <p class="text-red-500 text-xs">No se puede realizar el pago porque existen inconsistencias en los datos.</p>
      </div>
    </div>

    <footer class="flex justify-end px-4 py-6 bg-indigo-200">
      <jet-button :disabled="!payments.length > 0 || processing" @click="store">
        <!-- Spin -->
        <svg
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          v-show="processing"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>

        <span v-show="processing" class="animate-pulse">Registrando Pagos</span>
        <span v-show="!processing">Registrar Pagos</span>
      </jet-button>
    </footer>
  </div>
</template>

<script>
import CashPayment from "./CashPayment.vue";
import PaymentList from "./NewInvoice/PaymentList.vue";
import { formatCurrency } from "@/utilities";
import JetButton from "@/Jetstream/Button.vue";
//import axios from "axios";
import Swal from "sweetalert2";

export default {
  components: { CashPayment, PaymentList, JetButton },
  props: {
    boxs: Array,
    invoice: Object,
  },
  emits: ["lockModal", "unlockModal", "updateInvoice"],
  data() {
    return {
      payments: [],
      error: false,
      processing: false,
    };
  },
  methods: {
    formatCurrency,
    addPayment(payment) {
      //Se busca si la caja ya está agregada
      if (this.payments.some((item) => item.box.id === payment.box.id)) {
        let index = this.payments.findIndex((item) => item.box.id === payment.box.id);
        this.payments[index].amount += payment.amount;
      } else {
        this.payments.push(payment);
      }

      this.sortPayemntList();
    },
    /**
     * Se encarga de poner al final de la lista los pagos
     * que están marcados para cambios.
     */
    sortPayemntList() {
      //console.log("handler");
      //Primero se ordenan de mayor a menor
      this.payments.sort((p1, p2) => {
        if (p1.amount > p2.amount) return -1;

        if (p1.amount < p2.amount) return 1;

        return 0;
      });

      //Despues se ponen al final los que están para pagos
      this.payments.sort((p1, p2) => {
        if (p1.useForChange && !p2.useForChange) return 1;
        if (!p1.useForChange && p2.useForChange) return -1;
        if (p1.useForChange && p2.useForChange) {
          if (p1.amount > p2.amount) return -1;

          if (p1.amount < p2.amount) return 1;

          return 0;
        }

        return 0;
      });
      //console.log(this.payments);
    },
    removePayment(index) {
      this.payments.splice(index, 1);
    },
    async store() {
      let data = this.getData();
      try {
        //Se cambia el estados
        this.processing = true;
        //Se notifica que se debe bloquear el modal
        this.$emit("lockModal");
        const res = await axios.put(route("invoice.storePayments"), data);
        if (res.data.ok) {
          this.$emit("updateInvoice", res.data.invoice);
          this.showNotification();
        } else {
          this.error = true;
          if (res.error) {
            console.log(res.data.invoice);
          } else if (res.data.invoice) {
            this.$emit("updateInvoice", res.data.invoice);
          }
        }
      } catch (error) {
        if (error.response) {
          if (error.response.data?.errors) {
            //this.createErrors(error.response.data?.errors);
            console.log(error.response.data?.errors);
            this.error = true;
          }
        } else if (error.request) {
          // La petición fue hecha pero no se recibió respuesta
          // `error.request` es una instancia de XMLHttpRequest en el navegador y una instancia de
          // http.ClientRequest en node.js
          console.log(error.request);
        } else {
          // Algo paso al preparar la petición que lanzo un Error
          console.log("Error", error.message);
        }
      } finally {
        this.$emit("unlockModal");
        this.processing = false;
      }
    },
    getData() {
      let invoiceId = this.invoice.id;
      let customerId = this.invoice.customer_id;
      let payments = [];

      this.payments.forEach((payment) => {
        let boxId = payment.box.id;
        let boxName = payment.box.name;
        let amount = payment.amount;
        let registerTransaction = payment.registerTransaction;

        payments.push({ boxId, boxName, amount, registerTransaction });
      });

      return { invoiceId, customerId, payments };
    },
    showNotification() {
      Swal.fire({
        title: `¡Pago Registrado!`,
        icon: "success",
        text: `EL pago a la factura ${this.invoice.invoice_number} fue registrado.`,
      });
    },
  },
};
</script>
