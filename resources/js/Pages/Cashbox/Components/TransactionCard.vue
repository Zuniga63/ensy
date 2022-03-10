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
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"
              />
            </svg>
          </a>
          <!-- Is blocked -->
          <a
            href="javascript:;"
            class="inline-block text-zinc-400"
            v-if="transaction.blocked"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                clip-rule="evenodd"
              />
            </svg>
          </a>
        </div>

        <!-- Show Details -->
        <a
          href="javascript:;"
          class="inline-block text-white"
          @click.stop="showDetails"
          :title="eyeTitle"
        >
          <!--i
            class="fas fa-exchange-alt"
            :class="{
              'fa-eye-slash': !showingDetails,
              'fa-eye': showingDetails,
            }"
          ></i-->
          <!-- Eye -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
            v-show="showingDetails"
          >
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
            <path
              fill-rule="evenodd"
              d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
              clip-rule="evenodd"
            />
          </svg>

          <!-- Eye off -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
            v-show="!showingDetails"
          >
            <path
              fill-rule="evenodd"
              d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
              clip-rule="evenodd"
            />
            <path
              d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
            />
          </svg>
        </a>
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
        <p class="text-gray-800 text-opacity-80" v-if="!createIsSameUpdate">
          Actualizado: {{ updatedAtFromNow }}
        </p>
      </div>

      <div class="flex justify-between" v-if="!transaction.blocked">
        <JetDangerButton @click="$emit('deleteTransaction', transaction)">Eliminar</JetDangerButton>
        <JetButton @click="$emit('updateTransaction', transaction)" v-if="!transaction.transfer" >Editar</JetButton>
      </div>
    </div>
  </div>
</template>
<script>
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";

export default {
  components: {
    JetButton,
    JetDangerButton,
  },
  emits: ["updateTransaction", "deleteTransaction"],
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
    createIsSameUpdate() {
      return this.transaction.createdAt.isSame(this.transaction.updatedAt);
    },
    createdAtFromNow() {
      return this.transaction.createdAt.fromNow();
    },
    updatedAtFromNow() {
      return this.transaction.updatedAt.fromNow();
    },
  },
};
</script>