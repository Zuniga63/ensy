<template>
  <div class="shadow h-40 overflow-y-auto">
    <table class="relative w-full divide-y divide-gray-200">
      <thead class="sticky top-0 bg-gray-100">
        <tr>
          <th
            scope="col"
            class="px-4 py-2 text-xs text-gray-700 tracking-wider text-center"
          >
            #
          </th>
          <th
            scope="col"
            class="px-4 py-2 text-xs text-gray-700 tracking-wider text-left"
          >
            Descripción
          </th>
          <th
            scope="col"
            class="px-4 py-2 text-xs text-gray-700 tracking-wider"
          >
            Cant.
          </th>
          <th
            scope="col"
            class="px-4 py-2 text-xs text-gray-700 tracking-wider"
          >
            Vlr. Unt
          </th>
          <!-- Descuento -->
          <th
            scope="col"
            class="px-4 py-2 text-xs text-gray-700 tracking-wider"
          >
            Descuento
          </th>
          <th
            scope="col"
            class="px-4 py-2 text-xs text-gray-700 tracking-wider"
          >
            Importe
          </th>
          <th scope="col" class="relative px-6 py-3">
            <span class="sr-only">Edit</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr
          v-for="(item, index) in itemList"
          :key="index"
          :class="{ 'bg-red-50': item.error }"
        >
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            {{ index + 1 }}
          </td>
          <td
            class="px-4 py-2 text-xs"
            :class="{
              'text-gray-800': !item.errors.description,
              'text-red-600': item.errors.description,
            }"
          >
            {{ item.description }}
          </td>
          <td
            class="px-4 py-2 text-xs text-center"
            :class="{
              'text-gray-800': !item.errors.quantity,
              'text-red-600': item.errors.quantity,
            }"
          >
            {{ item.quantity }}
          </td>
          <td
            class="px-4 py-2 text-xs text-center"
            :class="{
              'text-gray-800': !item.errors.unitValue,
              'text-red-600': item.errors.unitValue,
            }"
          >
            {{ formatCurrency(item.unitValue) }}
          </td>
          <td
            class="px-4 py-2 text-xs text-center"
            :class="{
              'text-gray-800': !item.errors.discount,
              'text-red-600': item.errors.discount,
            }"
          >
            <p v-if="item.discount">
              {{ item.discount ? formatCurrency(item.discount) : "" }}
              <span class="text-xs text-green-500"
                >({{ item.discountPercentage }}%)</span
              >
            </p>
          </td>
          <td
            class="px-4 py-2 text-xs text-center"
            :class="{
              'text-gray-800': !item.errors.amount,
              'text-red-600': item.errors.amount,
            }"
          >
            {{ formatCurrency(item.amount) }}
          </td>
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            <div class="flex items-center justify-center">
              <!-- Delete Item -->
              <a
                href="javascript:;"
                class="
                  text-red-500
                  p-1
                  rounded-full
                  hover:bg-red-100
                  transition-colors
                "
                @click="$emit('removeItem', index)"
              >
                <delete-icon class="h-4 w-4" />
              </a>

              <!-- Update Item -->
              <a
                href="javascript:;"
                class="
                  hidden
                  text-indigo-500
                  p-1
                  rounded-full
                  hover:bg-indigo-100
                  transition-colors
                "
              >
                <edit-icon class="h-4 w-4" />
              </a>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
import EditIcon from "@/Components/Svgs/Edit.vue";
import DeleteIcon from "@/Components/Svgs/Trash.vue";

export default {
  components: {
    EditIcon,
    DeleteIcon,
  },
  props: ["itemList", "errors"],
  emits: ["removeItem"],
  methods: {
    /**
     *  Se encarga de formatear el numero a moneda
     * @param {string} numero a formatear.
     */
    formatCurrency(number) {
      let fractionDigits = 0;
      let style = "currency";
      let currency = "COP";
      const formater = new Intl.NumberFormat("es-CO", {
        style,
        currency,
        minimumFractionDigits: fractionDigits,
      });
      return formater.format(number);
    },
  },
};
</script>