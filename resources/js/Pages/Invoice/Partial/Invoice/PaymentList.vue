<template>
  <div class="shadow h-40 overflow-y-auto">
    <table class="relative w-full divide-y divide-gray-200">
      <thead class="sticky top-0 bg-gray-100 z-10">
        <tr>
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider text-center">#</th>
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider text-center">Fecha</th>
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider text-left">Descripción</th>
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider">Importe</th>
          <th scope="col" class="relative px-6 py-3">
            <span class="sr-only">Edit</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(item, index) in payments" :key="index" :class="{ 'opacity-50 line-through': item.cancel }" :title="item.cancel_message">
          <!-- # -->
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            {{ index + 1 }}
          </td>
          <!-- Date -->
          <td class="px-4 py-2 text-xs text-center text-gray-800">
            {{ formatDate(item.payment_date) }}
          </td>
          <!-- Description -->
          <td class="px-4 py-2 text-xs text-gray-800">
            {{ item.description }}
          </td>
          <!-- Importe -->
          <td class="px-4 py-2 text-xs text-center text-gray-800">
            {{ formatCurrency(item.amount) }}
          </td>
          <!-- Actions -->
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            <div class="flex items-center justify-center">
              <!-- Delete Item -->
              <a
                href="javascript:;"
                class="text-red-500 p-1 rounded-full hover:bg-red-100 transition-colors"
                @click="$emit('cancelPayment', item)"
                v-if="!item.cancel"
              >
                <delete-icon class="h-4 w-4" />
              </a>

              <!-- Update Item -->
              <a
                href="javascript:;"
                class="hidden text-indigo-500 p-1 rounded-full hover:bg-indigo-100 transition-colors"
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
import DeleteIcon from "@/Components/Svgs/Trash.vue";
import EditIcon from "@/Components/Svgs/Edit.vue";
import { formatCurrency } from "@/utilities";
import dayjs from "dayjs";
export default {
  components: { DeleteIcon, EditIcon },
  emits: ["cancelPayment"],
  props: {
    payments: Array,
  },
  methods: {
    formatCurrency,
    formatDate(date) {
      const DATE_FORMAT = "DD-MM-YYYY hh:mm a";

      return dayjs(date).format(DATE_FORMAT);
    },
  },
};
</script>