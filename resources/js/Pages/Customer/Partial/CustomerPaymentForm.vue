<template>
  <form @submit.prevent="submit" class="px-4 py-6">
    <header class="pb-2 mb-4 border-b-8 border-gray-400 border-double">
      <h1 class="text-gray-600 font-bold mb-1">Registrar Pago</h1>
      <p class="text-gray-600 text-xs">
        Se va a realizar un abono al cliente <span class="font-bold"> {{ customer.full_name }} </span> que tiene en su
        haber {{ customer.invoices.length }} facturas pendientes con un saldo total de
        <span class="font-bold"> {{ formatCurrency(customer.balance) }} </span>.
      </p>
    </header>

    <div class="pb-4 mb-4 border-b-8 border-gray-400 border-double">
      <!-- Deposit box -->
      <div class="mb-2">
        <label-component required value="Caja" for="paymentBox" class="mb-2 text-gray-800" />
        <select
          class="
            w-full
            border border-gray-200
            rounded
            focus:ring focus:ring-indigo-400 focus:ring-opacity-40
            transition-opacity
            text-gray-800
          "
          v-model="form.cashboxId"
          id="paymentBox"
        >
          <option :value="box.id" v-for="box in boxs" :key="box.id">{{ box.name }}</option>
        </select>

        <jet-input-error :message="form.errors.cashboxId" class="mt-2" />
      </div>

      <!-- Importe a pagar -->
      <div class="mb-2">
        <label-component required value="Valor a Abonar" for="paymentAmount" class="mb-2 text-gray-800" />
        <currency-input
          placeholder="Escribe el valor aquí."
          id="paymentAmount"
          type="text"
          class="text-gray-800 text-right"
          v-model="form.amount"
        />

        <jet-input-error :message="form.errors.amount" class="mt-1" />
      </div>
    </div>

    <footer class="flex justify-end">
      <JetButton :disabled="form.processing">
        <!-- Spin -->
        <spin-icon v-show="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />

        <!-- Message -->
        <span v-show="!form.processing">Registrar Pago</span>
        <span v-show="form.processing">Procesando...</span>
      </JetButton>
    </footer>
  </form>
</template>

<script>
import LabelComponent from "@/Components/Form/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import SpinIcon from "@/Components/Svgs/Spin.vue";
import { formatCurrency } from "@/utilities";
import Swal from "sweetalert2";

export default {
  props: ["customer", "boxs"],
  emits: ["hiddenForm", "lockModal", "unlockModal"],
  components: {
    LabelComponent,
    JetInputError,
    JetButton,
    CurrencyInput,
    SpinIcon,
  },
  setup(props) {
    const form = useForm({
      cashboxId: props.boxs.length ? props.boxs[0].id : null,
      amount: null,
    });

    return { form, formatCurrency };
  },
  methods: {
    submit() {
      this.form.post(route("customer.storePayment", this.customer.id), {
        preserveScroll: true,
        preserveState: true,
        onStart: () => {
          this.$emit("lockModal");
        },
        onSuccess: (page) => {
          this.$emit("hiddenForm");
          let res = page.props.flash.message;
          if (res.ok) {
            //Se contruye el mensaje

            let message = `Se ha registrado con éxito`;

            if (res.paidCount > 0) {
              message += " el pago de " + res.paidCount + " ";
              message += res.paidCount > 1 ? "facturas" : "factura";

              if (res.paymentCount > 0) message += " y";
            }

            if (res.paymentCount > 0) {
              message += " el abono de " + res.paymentCount + " ";
              message += res.paymentCount > 1 ? "facturas" : "factura";
            }
            message += ".";

            this.$emit("hiddenForm");
            Swal.fire({
              icon: "success",
              title: "¡Transferencia Exitosa!",
              html: message,
            });
          }
        },
        onFinish: () => {
          this.$emit("unlockModal");
        },
      });
    },
  },
};
</script>