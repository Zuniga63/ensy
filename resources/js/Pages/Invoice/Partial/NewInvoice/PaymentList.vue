<template>
  <div class="shadow h-40 overflow-y-auto">
    <table class="w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <!-- # -->
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider">#</th>
          <!-- Box -->
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider text-left">Caja</th>
          <!-- RegisterTransaction -->
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider text-center">
            <div class="flex flex-col">
              <span>Registrar</span>
              <span>Transacción</span>
            </div>
          </th>
          <!-- Use for Change -->
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider text-center">
            <div class="flex flex-col">
              <span>Utilizar</span>
              <span>Para Cambio</span>
            </div>
          </th>
          <!-- Amount -->
          <th scope="col" class="px-4 py-2 text-xs text-gray-700 tracking-wider">Importe</th>
          <!-- Actions -->
          <th scope="col" class="relative px-6 py-3">
            <span class="sr-only">Edit</span>
          </th>
        </tr>
      </thead>
      <tbody class="relative bg-white divide-y divide-gray-200">
        <tr v-for="(payment, index) in payments" :key="index">
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            {{ index + 1 }}
          </td>
          <td class="px-4 py-2 text-xs text-gray-800">
            {{ payment.box.name }}
          </td>
          <td class="text-center">
            <jet-checkbox v-model:checked="payment.registerTransaction" />
          </td>
          <td class="text-center">
            <jet-checkbox v-model:checked="payment.useForChange" @change="$emit('updateOrder')" />
          </td>
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            {{ formatCurrency(payment.amount) }}
          </td>
          <td class="px-4 py-2 text-xs text-gray-800 text-center">
            <div class="flex items-center justify-center">
              <!-- Delete Item -->
              <a
                href="javascript:;"
                class="text-red-500 p-1 rounded-full hover:bg-red-100 transition-colors"
                @click="$emit('removePayment', index)"
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
import EditIcon from "@/Components/Svgs/Edit.vue";
import DeleteIcon from "@/Components/Svgs/Trash.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";

export default {
  components: {
    EditIcon,
    DeleteIcon,
    JetCheckbox,
  },
  props: ["payments"],
  emits: ["updateOrder", "removePayment"],
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