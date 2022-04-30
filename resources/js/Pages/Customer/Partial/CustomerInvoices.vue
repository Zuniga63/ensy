<template>
  <div class="shadow border-b border-gray-300 overflow-y-auto overflow-x-auto min-h-[15rem] max-h-screen">
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
          <!-- Descripción -->
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
          <th
            scope="col"
            class="px-3 sm:px-6 py-3 text-center text-xs sm:text-sm text-gray-500 tracking-wider uppercase"
          >
            Saldo
          </th>

          <th scope="col" class="hidden lg:table-cell relative px-6 py-3">
            <span class="sr-only">Actions</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="invoice in filterList" :key="invoice.id" class="transition-colors hover:bg-indigo-50">
          <!-- Index -->
          <td class="hidden lg:table-cell px-3 py-2 text-gray-800 text-center text-sm">
            {{ invoice.invoice_number }}
          </td>

          <!-- Date -->
          <td class="px-3 py-2 text-gray-800 text-xs lg:text-sm text-center whitespace-nowrap">
            <span class="lg:hidden"> {{ formatDate(invoice.expedition_date, true) }} </span>
            <span class="hidden lg:inline-block"> {{ formatDate(invoice.expedition_date) }} </span>
          </td>

          <td
            class="hidden lg:table-cell px-3 py-2 text-gray-600 text-letf italic"
            :class="{
              'text-xs': invoice.items.length > 1,
              'text-sm': invoice.items.legth <= 1,
            }"
          >
            <div v-for="item in invoice.items" :key="item.id" :class="{ 'line-through': item.cancel }">
              <span> {{ item.quantity }} {{ item.description }} {{ formatCurrency(calculatePrice(item)) }} </span>
            </div>
          </td>
          <td class="px-3 py-2 text-gray-800 text-right text-xs sm:text-sm lg:text-base">
            {{ formatCurrency(invoice.amount) }}
          </td>
          <td class="px-3 py-2 text-gray-800 text-right text-xs sm:text-sm lg:text-base">
            {{ formatCurrency(invoice.balance) }}
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
  props: ["invoices", "filterBy"],
  methods: {
    formatCurrency,
    formatDate(date, short = false) {
      date = dayjs(date);
      if (short) return date.format("DD-MM-YY");

      return date.format("DD-MM-YYYY");
    },
    /**
     * Se encarga de filtrar las facturas segun si están pagadas,
     * pendientes o anuladas.
     * @param {array} list listado de facturas.
     * @return {array}
     */
    filterByGeneral(list) {
      if (this.filterBy === "all") return list;
      if (this.filterBy === "paid") return list.filter((item) => !item.balance && !item.cancel);
      if (this.filterBy === "pending") return list.filter((item) => item.balance && !item.cancel);
      if (this.filterBy === "canceled") return list.filter((item) => item.cancel);

      return [];
    },
    calculatePrice(item) {
      const price = parseFloat(item.unit_value);
      const discount = item.discount ? parseFloat(item.discount) : 0;

      return price - discount;
    },
  }, //.end mthod
  computed: {
    filterList() {
      let list = this.invoices.slice();
      list = this.filterByGeneral(list);
      return list;
    },
  },
};
</script>
