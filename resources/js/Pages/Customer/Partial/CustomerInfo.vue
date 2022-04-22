<template>
  <div class="bg-white px-4 py-6 lg:border lg:border-gray-200 shadow rounded-lg">
    <div class="grid grid-cols-2 gap-2">
      <!-- Imagen del cliente -->
      <div class="col-span-2 mb-2">
        <div class="flex justify-center w-full">
          <img :src="customer.image_url" :alt="customer.full_name" class="w-1/4 rounded-full shadow" />
        </div>
      </div>

      <!-- Nombres -->
      <div>
        <p class="text-gray-600 text-xs tracking-wide">Nombres</p>
        <p class="text-sm text-gray-800">{{ customer.first_name }}</p>
      </div>

      <!-- Apellidos -->
      <div>
        <p class="text-gray-600 text-xs tracking-wide">Apellidos</p>
        <p class="text-sm text-gray-800">{{ customer.last_name }}</p>
      </div>

      <!-- Identificación -->
      <div class="col-span-2" v-if="customer.document_number">
        <p class="text-gray-600 text-xs tracking-wider">Documento</p>
        <p class="text-sm text-gray-800 tracking-wider">
          <span class="inline-block mr-2 italic"> {{ customer.document_type }} </span>
          <span> {{ formatDocument(customer.document_number) }} </span>
        </p>
      </div>

      <!-- Email -->
      <div class="col-span-2" v-if="customer.email">
        <p class="text-gray-600 text-xs tracking-wider">Email</p>
        <p class="text-sm text-gray-800 italic truncate" @click="selectText">
          {{ customer.email }}
        </p>
      </div>

      <!-- Saldo -->
      <div class="col-span-2" v-if="customer.balance">
        <h3 class="text-center text-gray-800 font-semibold mb-1">Saldo</h3>
        <p class="text-center text-gray-800 text-lg font-bold">{{ formatCurrency(customer.balance) }}</p>
      </div>

      <!-- Auditoría -->
      <div class="col-span-2 text-xs text-gray-400">
        <p>Registrado: {{ createdAt.format("ddd DD-MM-YYYY") }} - {{ createdAt.fromNow() }}</p>
        <p v-if="!updateIsSameCreate">Actualizado {{ updatedAt.fromNow() }}</p>
      </div>

      <!-- Actions -->
      <div class="col-span-2 mt-4">
        <button
          v-if="customer.balance"
          class="
            block
            w-full
            lg:w-3/4
            mx-auto
            px-4
            py-2
            border border-indigo-600
            rounded-lg
            font-bold
            text-sm text-indigo-800
            hover:text-white
            bg-transparent
            hover:bg-indigo-600
            active:bg-indigo-800
            tracking-wider
            transition-colors
            shadow-lg
          "
          @click="$emit('addNewPayment')"
        >
          Registrar Abono
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDocument, formatCurrency, selectText } from "@/utilities";

import dayjs from "dayjs";
import "dayjs/locale/es-do";
import relativeTime from "dayjs/plugin/relativeTime";

export default {
  props: ["customer"],
  emits: ["addNewPayment"],
  setup(props) {
    dayjs.locale("es-do");
    dayjs.extend(relativeTime);
  },
  methods: {
    formatDocument,
    formatCurrency,
    selectText,
  },
  computed: {
    createdAt() {
      return dayjs(this.customer.created_at);
    },
    updatedAt() {
      return dayjs(this.customer.updated_at);
    },
    updateIsSameCreate() {
      return this.createdAt.isSame(this.updatedAt);
    },
  },
};
</script>
