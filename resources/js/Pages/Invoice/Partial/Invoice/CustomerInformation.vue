<template>
  <div class="grid grid-cols-3 gap-4 p-3 border border-gray-200 rounded-md bg-gray-50 text-sm shadow">
    <!-- Nombre del cliente -->
    <div class="col-span-2">
      <div class="flex">
        <p class="mr-2 text-gray-800">Cliente:</p>
        <p class="w-full border-b border-gray-800 text-center text-gray-800 uppercase">
          {{ invoice.customer_name }}
        </p>
      </div>
    </div>

    <!-- Nit -->
    <div class="col-span-1">
      <div class="flex">
        <p class="mr-2 text-gray-800">Nit:</p>
        <p class="w-full border-b border-gray-800 text-center text-gray-800 uppercase tracking-wider">
          {{ formatDocument(invoice.customer_document) }}
        </p>
      </div>
    </div>

    <!-- Address -->
    <div class="col-span-2">
      <div class="flex">
        <p class="mr-2 text-gray-800">Dirección:</p>
        <p class="w-full border-b border-gray-800 text-center text-gray-800 uppercase">
          {{ invoice.customer_address }}
        </p>
      </div>
    </div>

    <!-- Phone -->
    <div class="col-span-1">
      <div class="flex">
        <p class="mr-2 text-gray-800">Telefono:</p>
        <p class="w-full border-b border-gray-800 text-center text-gray-800 uppercase tracking-wider">
          {{ formatPhone(invoice.customer_phone) }}
        </p>
      </div>
    </div>

    <!-- Seller -->
    <div class="col-span-3">
      <div class="flex">
        <p class="mr-2 text-gray-800">Vendedor:</p>
        <p class="w-full border-b border-gray-800 text-center text-gray-800 uppercase">
          {{ invoice.seller_name }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDocument, formatPhone } from "@/utilities.js";
export default {
  props: ["invoice"],
  methods: {
    formatDocument,
    formatPhone,
  },
};
</script>
