<template>
  <div class="shadow border-b border-gray-300 overflow-y-auto overflow-x-auto max-h-screen">
    <table class="relative min-w-full table-auto">
      <thead class="sticky top-0 bg-gray-50">
        <tr>
          <!-- # -->
          <th scope="col" class="hidden lg:table-cell px-6 py-3 text-center text-gray-500 text-sm">#</th>
          <!-- Fecha-->
          <th
            scope="col"
            class="px-3 sm:px-6 py-3 text-center text-xs sm:text-sm text-gray-500 tracking-wider uppercase"
          >
            Fecha
          </th>
          <!-- Subtotal -->
          <th
            scope="col"
            class="hidden lg:table-cell px-6 py-3 text-center text-sm text-gray-500 tracking-wider uppercase"
          >
            Descripción
          </th>
          <!-- Valor -->
          <th
            scope="col"
            class="px-3 sm:px-6 py-3 text-center text-xs sm:text-sm text-gray-500 tracking-wider uppercase"
          >
            Importe
          </th>

          <th scope="col" class="hidden lg:table-cell relative px-6 py-3">
            <span class="sr-only">Actions</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(payment, index) in payments" :key="payment.id" class="transition-colors hover:bg-indigo-50">
          <!-- Index -->
          <td class="hidden lg:table-cell px-3 py-2 text-gray-800 text-center text-sm">
            {{ index + 1 }}
          </td>

          <!-- Date -->
          <td class="px-3 py-2 text-gray-800 text-xs lg:text-sm">
            <span class="lg:hidden"> {{ formatDate(payment.payment_date, true) }} </span>
            <span class="hidden lg:inline-block"> {{ formatDate(payment.payment_date) }} </span>
          </td>

          <td class="px-3 py-2 text-gray-800 text-xs lg:text-sm">
            {{ payment.description }}
          </td>

          <td class="hidden lg:table-cell px-3 py-2 text-gray-800 text-right text-xs sm:text-sm lg:text-base">
            {{ formatCurrency(payment.amount) }}
          </td>

          <!-- Acciones -->
          <td class="hidden lg:table-cell px-3 py-2">
            <!-- <div class="flex justify-end">
              <row-button type="show" class="mr-2" :href="route('customer.show', customer.id)" title="Ver Cliente" />
              <row-button type="edit" class="mr-2" title="Editar Cliente" :href="route('customer.edit', customer.id)" />
              <row-button type="delete" @click="deleteCustomer(customer)" title="Eliminar Cliente" />
            </div> -->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { formatCurrency } from "@/utilities";
import dayjs from "dayjs";
export default {
  props: ["payments"],
  methods: {
    formatCurrency,
    formatDate(date, short = false) {
      date = dayjs(date);
      if (short) return date.format("DD-MM-YY");

      return date.format("DD-MM-YYYY");
    },
  },
};
</script>
