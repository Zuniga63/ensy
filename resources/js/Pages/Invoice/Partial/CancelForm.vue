<template>
  <form @submit.prevent="submit">
    <header class="px-4 py-2 bg-red-100">
      <h2 class="text-red-600 text-center font-bold uppercase tracking-wide">{{ title }}</h2>
      <!-- Description for Payments -->
      <p class="text-xs text-gray-600" v-if="type === 'payment'">
        Se va a anular el pago realizado el
        <span class="font-semibold">{{ formatDate(payment.payment_date) }}</span> por el cliente
        <span class="font-semibold"> {{ invoice.customer?.full_name || invoice.customer_name }} </span> a la factura
        <span class="text-red-600 font-semibold">N° {{ invoice.invoice_number }} </span>
        por valor de <span class="font-semibold">{{ formatCurrency(payment.amount) }}</span>
      </p>

      <p class="text-xs text-gray-600" v-if="type === 'item'">
        Se va a anular el articulo <span class="font-semibold">{{ item.description }}</span> por valor de
        <span class="font-semibold">
          {{ formatCurrency(item.hasDiscount ? item.priceWithDiscount : item.price) }}
        </span>
        de la factura <span class="text-red-600 font-semibold">N° {{ invoice.invoice_number }} </span> a nombre del
        cliente <span class="font-semibold"> {{ invoice.customer?.full_name || invoice.customer_name }} </span>.
      </p>

      <p class="text-xs text-gray-600" v-if="type === 'invoice'">
        Se va a anular la factura <span class="text-red-600 font-semibold">N° {{ invoice.invoice_number }} </span> por
        valor de <span class="font-semibold">{{ formatCurrency(invoice.amount) }}</span> a nombre del cliente
        <span class="font-semibold"> {{ invoice.customer?.full_name || invoice.customer_name }} </span>.
      </p>
    </header>

    <div class="px-4 py-6">
      <!-- Cancel Description -->
      <div class="mb-2">
        <custom-label required value="Motivo de cancelación" class="text-gray-800 text-sm mb-2" for="cancelMessage" />
        <jet-input
          type="text"
          class="w-full text-gray-800 text-xs"
          placeholder="Escribelo aquí."
          id="cancelMessage"
          v-model="message"
          ref="message"
        />
        <input-error :message="errors.message" class="text-xs mt-1 ml-2"></input-error>
      </div>

      <!-- Password -->
      <div class="mb-2">
        <custom-label required value="Contraseña" class="text-gray-800 text-sm mb-2" for="cancelPassword" />
        <jet-input
          type="password"
          class="w-full text-gray-800 text-xs"
          placeholder="Escribe tú contraseña."
          id="cancelPassword"
          v-model="password"
          ref="password"
        />
        <input-error :message="errors.password" class="text-xs mt-1 ml-2"></input-error>
      </div>

      <!-- Quantity -->
      <div class="grid grid-cols-3 mb-2" v-if="type === 'item'">
        <div class="col-span-1">
          <custom-label required value="Cant." class="text-gray-800 text-sm mb-2" for="cancelQuantity" />
          <jet-input
            type="number"
            class="w-full text-gray-800 text-center text-xs"
            id="cancelQuantity"
            v-model.number="quantity"
            ref="message"
            min="0.001"
            :max="item.quantity"
            step="0.001"
          />
        </div>
        <input-error :message="errors.quantity" class="col-span-3 text-xs mt-1 ml-2"></input-error>
      </div>

      <!-- Notas -->
      <div>
        <!-- Notas para la anulación de pagos -->
        <div v-if="type === 'payment'" class="p-2 bg-gray-200 rounded shadow mt-4">
          <p class="text-gray-600 text-xs">
            <span class="text-red-600">Nota:</span> Anular un pago implica la eliminación de la transacción asociada,
            solo si el pago no ha sido bloqueado, en cuyo caso se registrará un nuevo movimiento indicando la salida del
            dinero de la caja.
          </p>
        </div>

        <!-- Notas para la anulación de articulos -->
        <div v-if="type === 'item'" class="p-2 bg-gray-200 rounded shadow mt-4">
          <p class="text-gray-600 text-xs">
            <span class="text-red-600">Nota 1:</span> Al anular un articulo de una factura, el valor del mismo es
            descontado y abonado al crédito. Si es mayor que el saldo, el excedente es registrado como un egreso de
            algunas de las cajas de la plataforma.
          </p>
        </div>

        <!-- Nota para la anulación de facturas -->
        <div v-if="type === 'invoice'" class="p-2 bg-gray-200 rounded shadow mt-4">
          <p class="text-gray-600 text-xs">
            <span class="text-red-600">Nota 1:</span> Al anular una factura todos los pagos y artículos son cancelados
            con el mismo concepto y las transacciones asociadas son eliminadas del sistema.
          </p>
        </div>
      </div>

      <input-error :message="errors.general" class="col-span-3 text-xs mt-1 ml-2"></input-error>

      <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      >
        <div class="flex mt-4" v-show="processing">
          <!-- Spin -->
          <spin-icon class="h-5 w-5 mr-2 text-gray-800" />

          <p class="text-gray-800 text-sm animate-pulse">
            <span v-if="type === 'payment'">Anulando pago.</span>
            <span v-if="type === 'item'">Anulando Item.</span>
            <span v-if="type === 'invoice'">Anulando Factura.</span>
          </p>
        </div>
      </transition>
    </div>

    <footer class="flex justify-between px-4 py-2 bg-red-100">
      <jet-button :disabled="processing" type="button" @click="$emit('close')">Cancelar</jet-button>
      <jet-danger-button :disabled="processing" type="submit">
        <span v-if="type === 'payment'">Anular Pago</span>
        <span v-if="type === 'item'">Anular Item</span>
        <span v-if="type === 'invoice'">Anular Factura</span>
      </jet-danger-button>
    </footer>
  </form>
</template>

<script>
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import InputError from "@/Components/Form/InputError.vue";
import JetInput from "@/Jetstream/Input.vue";
import dayjs from "dayjs";
import locale_es_do from "dayjs/locale/es-do";
import { formatCurrency } from "@/utilities";
import SpinIcon from "@/Components/Svgs/Spin.vue";
//import axios from "axios";
import Swal from "sweetalert2";

export default {
  components: { JetButton, JetDangerButton, CustomLabel, InputError, JetInput, SpinIcon },
  emits: ["lockModal", "unlockModal", "updateInvoice", "close"],
  props: {
    invoice: Object,
    item: Object,
    payment: Object,
    type: String,
  },
  setup() {
    dayjs.locale(locale_es_do);
  },
  data() {
    return {
      message: null,
      password: null,
      quantity: this.item?.quantity,
      errors: {
        message: null,
        password: null,
        quantity: null,
        general: null,
      },
      processing: false,
    };
  },
  methods: {
    formatDate(date) {
      return dayjs(date).format("dddd DD [de] MMMM [de] YYYY [a las] hh:mm a");
    },
    formatCurrency,
    getData() {
      let url = null;
      let data = {
        invoiceId: this.invoice.id,
        paymentId: this.payment?.id,
        itemId: this.item?.id,
        quantity: this.quantity,
        message: this.message,
        password: this.password,
      };

      if (this.type === "payment") {
        url = route("invoice.cancelPayment");
      } else if (this.type === "item") {
        url = route("invoice.cancelItem");
      } else if (this.type === "invoice") {
        url = route("invoice.cancel");
      }

      return { url, data };
    },
    async submit() {
      let data = this.getData();

      if (data.url) {
        this.processing = true;
        this.$emit("lockModal");
        try {
          const res = await axios.put(data.url, data.data);
          if (res.data.ok) {
            console.log(res.data);
            this.$emit("updateInvoice", res.data.invoice);
            this.showNotification();
          } else {
            this.errors.general = res.data.message;
            console.log(res.data);
          }
        } catch (error) {
          if (error.response) {
            console.log(error.response);
            this.manageErrors(error.response.data?.errors);
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
          this.processing = false;
          this.$emit("unlockModal");
        }
      }
    },
    manageErrors(errors) {
      if (errors.invoiceId || errors.paymentId || errors.itemId) {
        console.log("Error: errores con las claves.");
      }
      this.errors.message = errors.message ? errors.message[0] : null;
      this.errors.password = errors.password ? errors.password[0] : null;
      this.errors.quantity = errors.quantity ? errors.quantity[0] : null;

      if (errors.message) this.$refs.message.focus();
      else if (errors.password) this.$refs.password.focus();
      else if (errors.quantity) this.$refs.quantity.focus();
    },
    showNotification() {
      let title = null;
      let message = null;

      if (this.type === "payment") {
        title = `¡Pago Anulado!`;
        message = `El pago a la factura N° ${this.invoice.invoice_number} fue anulado.`;
      } else if (this.type === "item") {
        title = `¡Articulo Anulado!`;
        message = `El articulo ${this.item.description} de la la factura N° ${this.invoice.invoice_number} fue anulado.`;
      }else if(this.type === "invoice"){
        title = "¡Factura Anulada!";
        message = `La factura N° ${this.invoice.invoice_number} fue anulada.`
      }

      if (title) {
        Swal.fire({
          title,
          icon: "success",
          text: message,
        });
      }
    },
  },
  computed: {
    title() {
      if (this.type === "invoice") return "Anular Factura de Venta";
      if (this.type === "payment") return "Anular Pago";
      if (this.type === "item") return "Anular Articulo";

      return "Titulo por defecto";
    },
  },
};
</script>