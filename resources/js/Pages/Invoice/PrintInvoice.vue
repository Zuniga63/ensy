<template>
  <div class="px-2 lg:max-w-3xl lg:mx-auto">
    <header class="flex flex-col items-center px-2 pt-6 pb-3 mb-3 border-b-2 border-gray-800">
      <!-- Logo -->
      <div class="w-9/12 mx-auto mb-4">
        <img :src="config.logo" :alt="config.name" class="block object-contain w-full h-full" />
      </div>

      <!-- Info -->
      <div>
        <h1 class="mb-2 text-gray-800 text-center font-bold tracking-wider italic">{{ config.name }}</h1>
        <p class="text-gray-600 text-center text-xs">
          Nit: <span class="font-semibold">{{ config.nit }}</span>
        </p>
        <p class="text-gray-600 text-center text-xs" v-if="config.address.show">{{ config.address.value }}</p>
        <p class="text-gray-600 text-center text-xs" v-if="config.phone.show">
          Tel: {{ formatPhone(config.phone.number) }}
        </p>
        <p class="text-gray-600 text-center text-xs" v-if="config.email.show">{{ config.email.value }}</p>
      </div>
    </header>

    <div class="mb-6">
      <!-- Invoice Information -->
      <div class="px-2 pb-3 mb-3 border-b-2 border-gray-800">
        <p class="text-gray-800 text-right uppercase font-bold mb-2">
          Factura de venta N° <span class="text-red-600">{{ invoice.invoice_number }}</span>
        </p>

        <!-- Expedition Date -->
        <div class="grid grid-cols-3 text-xs text-gray-800 tracking-wide">
          <div class="flex justify-between">
            <p>Fecha</p>
            :
          </div>
          <p class="col-span-2 text-right">{{ formatDate(invoice.expedition_date) }}</p>
        </div>
        <!-- Customer -->
        <div class="grid grid-cols-3 text-xs text-gray-800 tracking-wider">
          <div class="flex justify-between">
            <p>Cliente</p>
            :
          </div>
          <p class="col-span-2 text-right line-clamp-1">{{ invoice.customer_name }}</p>
        </div>
        <!-- Customer Nit o CC -->
        <div class="grid grid-cols-3 text-xs text-gray-800 tracking-wider">
          <div class="flex justify-between">
            <p>CC / Nit</p>
            :
          </div>
          <p class="col-span-2 text-right line-clamp-1">{{ formatDocument(invoice.customer_document) }}</p>
        </div>
        <!-- Customer Addres -->
        <div class="grid grid-cols-3 text-xs text-gray-800 tracking-wider">
          <div class="flex justify-between">
            <p>Dirección</p>
            :
          </div>
          <p class="col-span-2 text-right line-clamp-1">{{ invoice.customer_address }}</p>
        </div>
      </div>

      <!-- Items -->
      <div class="px-2 pb-3 mb-3 border-b-2 border-gray-800">
        <!-- Header -->
        <header class="flex text-xs uppercase font-bold text-gray-800">
          <p>Cant.</p>
          <p class="flex-grow text-center">Descripción</p>
          <p>Vlr. Unt</p>
        </header>

        <div class="mb-2 text-xs uppercase text-gray-800 divide-y divide-gray-400">
          <div class="grid grid-cols-12 items-center py-1" v-for="item in invoice.items" :key="item.id">
            <p class="col-span-1 text-center">{{ item.quantity }}</p>
            <p class="col-span-8">{{ item.description }}</p>
            <div class="col-span-3 text-right">
              <p
                :class="{
                  'line-through text-gray-400': item.discount,
                }"
              >
                {{ formatCurrency(item.unit_value) }}
              </p>
              <p v-if="item.discount">{{ priceWithDiscount(item) }}</p>
            </div>
          </div>
        </div>

        <footer class="pt-2 border-t-2 border-gray-500 border-dotted">
          <!-- Subtotal -->
          <div class="grid grid-cols-3 gap-2 text-gray-800 tracking-wider" v-if="invoice.subtotal">
            <p class="col-span-2 text-right text-xs text-gray-800">Subtotal:</p>
            <p class="text-xs text-right">{{ formatCurrency(invoice.subtotal) }}</p>
          </div>
          <!-- Discount -->
          <div class="grid grid-cols-3 gap-2 text-gray-800 tracking-wider" v-if="invoice.discount">
            <p class="col-span-2 text-right text-xs text-gray-800">Descuento:</p>
            <p class="text-xs text-right">{{ formatCurrency(invoice.discount) }}</p>
          </div>
          <!-- Amount -->
          <div class="grid grid-cols-3 gap-2 text-gray-800 font-bold tracking-wider">
            <p class="col-span-1 text-right text-base text-gray-800">Total:</p>
            <p class="col-span-2 text-right">{{ formatCurrency(invoice.amount) }}</p>
          </div>
        </footer>
      </div>

      <!-- Forma de pago -->
      <div class="px-4">
        <h3 class="mb-2 text-xs text-center text-gray-800 font-bold tracking-wider">Forma de Pago</h3>
        <!-- Cash -->
        <div class="flex justify-end text-sm text-gray-800 tracking-wider" v-if="invoice.cash">
          <p class="mr-2">Efectivo:</p>
          <p>{{ formatCurrency(invoice.cash) }}</p>
        </div>
        <!-- Credit -->
        <div class="flex justify-end text-sm text-gray-800 tracking-wider" v-if="invoice.credit">
          <p class="mr-2">Credito:</p>
          <p>{{ formatCurrency(invoice.credit) }}</p>
        </div>
        <!-- Change -->
        <div class="flex justify-end mt-4 text-base text-gray-800 tracking-wider font-bold">
          <p class="mr-2 flex-grow text-center">Cambio:</p>
          <p>{{ formatCurrency(invoice.cash) }}</p>
        </div>
      </div>
    </div>

    <footer class="mb-2 text-xs text-gray-600 text-center">
      <p>Elaborada por: {{ invoice.seller_name }}</p>
      <p>{{ config.name }}</p>
    </footer>
  </div>
</template>

<script>
import dayjs from "dayjs";
import { formatCurrency, formatDocument, formatPhone } from "@/utilities";
export default {
  props: ["invoice", "config"],
  methods: {
    formatCurrency,
    formatDocument,
    formatPhone,
    formatDate(date) {
      return dayjs(date).format("DD-MM-YYYY hh:mm a");
    },
    priceWithDiscount(item) {
      return formatCurrency(parseFloat(item.unit_value) - parseFloat(item.discount));
    },
  },
  mounted(){
    print();
  }
};
</script>
