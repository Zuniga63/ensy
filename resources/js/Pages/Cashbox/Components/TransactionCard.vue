<template>
  <div class="border border-gray-400 rounded mb-2 overflow-hidden">
    <header
      class="flex justify-between items-center p-2 bg-slate-800 text-white"
    >
      <div>
        <!-- Title -->
        <h3 class="text-sm font-bold">
          {{ date }}
          <span
            v-show="!showingDetails"
            class="inline-block ml-2"
            :class="{
              'text-green-200': transaction.amount >= 0,
              'text-red-200': transaction.amount < 0,
            }"
          >
            {{ formatCurrency(transaction.amount) }}
          </span>
        </h3>
        <!-- Description -->
        <p class="-mt-1" v-show="showingDetails">
          <span class="text-xs">{{ time }}</span>
          -
          <span class="text-xs text-white text-opacity-80">
            {{ dateFromNow }}
          </span>
        </p>

        <p
          class="text-sm text-white text-opacity-90 line-clamp-1"
          v-show="!showingDetails"
        >
          {{ transaction.description }}
        </p>
      </div>

      <div class="flex flex-nowrap">
        <!-- Si la tranacciones una transferencia o está bloqueda -->
        <div v-show="showingDetails" class="flex flex-nowrap mr-2">
          <!-- Is a transfer -->
          <a
            href="javascript:;"
            class="inline-block text-green-700"
            :class="{ 'mr-2': transaction.blocked }"
            v-if="transaction.transfer"
            ><i class="fas fa-exchange-alt"></i
          ></a>
          <!-- Is blocked -->
          <a
            href="javascript:;"
            class="inline-block text-zinc-400"
            v-if="transaction.blocked"
            ><i class="fas fa-lock"></i
          ></a>
        </div>

        <!-- Show Details -->
        <a
          href="javascript:;"
          class="inline-block text-white"
          @click.stop="showDetails"
          :title="eyeTitle"
        >
          <i
            class="fas fa-exchange-alt"
            :class="{
              'fa-eye-slash': !showingDetails,
              'fa-eye': showingDetails,
            }"
          ></i
        ></a>
      </div>
    </header>

    <!-- Body -->
    <div class="p-2" v-show="showingDetails">
      <p class="text-base text-gray-800">
        {{ transaction.description }}
      </p>
      <div class="grid grid-cols-2 gap-2">
        <!-- Importe -->
        <div class="p-1 text-center">
          <p class="text-gray-700 text-sm font-bold">Importe</p>
          <p
            class="text-sm"
            :class="{
              'text-green-700': isAIncome,
              'text-rose-700': !isAIncome,
            }"
          >
            {{ formatCurrency(transaction.amount) }}
          </p>
        </div>
        <div class="p-1 text-center">
          <p class="text-gray-700 text-sm font-bold">Saldo</p>
          <p class="text-gray-700 text-sm">
            {{ formatCurrency(transaction.balance) }}
          </p>
        </div>
      </div>

      <div class="text-xs mb-2">
        <p class="text-gray-800 text-opacity-80">
          Creado: {{ createdAtFromNow }}
        </p>
        <p
          class="text-gray-800 text-opacity-80"
          v-if="!createIsSameUpdate"
        >
          Actualizado: {{ updatedAtFromNow }}
        </p>
      </div>

      <div class="flex justify-between">
        <JetDangerButton @click="destroy">Eliminar</JetDangerButton>
        <JetButton>Editar</JetButton>
      </div>
    </div>
  </div>
</template>
<script>
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
  components: {
    JetButton,
    JetDangerButton,
  },
  emits:['deleteTransaction'],
  props: ["transaction"],
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
      showingDetails: false,
    };
  },
  methods: {
    formatCurrency(number) {
      return this.formater.format(number);
    },
    showDetails() {
      this.showingDetails = !this.showingDetails;
    },
    destroy(){
      let name = 'cashbox.destroyTransaction';
      let parameters = [this.transaction.cashbox_id, this.transaction.id];
      let url = route(name, parameters);
      console.log(url);
      Inertia.delete(url, {
        preserveScroll: true,
        preserveState: true,
      });
    }
  },
  computed: {
    date() {
      if (!this.showingDetails) {
        return this.transaction.date.format("DD-MM-YYYY");
      }

      return this.transaction.date.format("dddd, DD-MM-YYYY");
    },
    time() {
      return this.transaction.date.format("hh:mm a");
    },
    dateFromNow() {
      return this.transaction.date.fromNow();
    },
    eyeTitle() {
      return this.showingDetails ? "Ocultar detalles" : "Mostrar Detalles";
    },
    isAIncome() {
      return this.transaction.amount >= 0 ? true : false;
    },
    createIsSameUpdate(){
      return this.transaction.createdAt.isSame(this.transaction.updatedAt);
    },
    createdAtFromNow(){
      return this.transaction.createdAt.fromNow();
    },
    updatedAtFromNow(){
      return this.transaction.updatedAt.fromNow();
    },

  },
};
</script>