<template>
  <tr class="hover:bg-indigo-50">
    <!-- ID -->
    <td class="px-3 py-2 text-center" :class="secondaryClass">
      {{ transaction.id }}
    </td>
    <!-- Date and Time -->
    <td class="px-3 py-2 whitespace-nowrap">
      <div class="text-center">
        <p class="text-sm" :class="mainClass">
          {{ date }}
        </p>
        <p class="text-xs" :class="secondaryClass">
          {{ dateFromNow }}
        </p>
      </div>
    </td>
    <!-- Description -->
    <td class="px-3 py-2">
      <p class="text-sm" :class="mainClass">
        {{ transaction.description }}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="inline-block h-5 w-5 ml-2 p-1 border bg-white rounded-full"
          :class="{
            'border-green-600': transaction.amount > 0,
            'border-orange-500': transaction.amount < 0,
          }"
          viewBox="0 0 20 20"
          fill="currentColor"
          v-if="transaction.transfer"
        >
          <path
            d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"
          />
        </svg>
      </p>
      <p class="text-xs" :class="secondaryClass">
        Creado: {{ createdAtFromNow }}
        <span v-if="!createIsSameUpdate"> - Actualizado: {{ updatedAtFromNow }} </span>
      </p>
    </td>
    <!-- Amount -->
    <td class="px-3 py-2 text-right">
      <span v-if="!transaction.transfer" :class="{ 'text-red-500': !isAIncome, 'text-green-500': isAIncome }">
        {{ formatCurrency(transaction.amount) }}
      </span>
      <span v-else :class="mainClass">
        {{ formatCurrency(transaction.amount) }}
      </span>
    </td>
    <!-- Balance -->
    <td class="px-3 py-2 text-right" :class="mainClass">
      {{ formatCurrency(transaction.balance) }}
    </td>
    <td class="px-3 py-2">
      <div class="flex justify-end" v-if="!transaction.blocked">
        <!-- Edit -->
        <row-button
          type="edit"
          class="mr-2"
          title="Editar Transacción"
          @click="$emit('updateTransaction', transaction)"
          v-if="!transaction.transfer"
        />
        <!-- Delete -->
        <row-button type="delete" title="Eliminar Transacción" @click="$emit('deleteTransaction', transaction)" />
      </div>
      <!-- Blocked Icon -->
      <div class="flex justify-end" v-else>
        <div class="border border-gray-400 rounded p-1 bg-gray-50 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path
              fill-rule="evenodd"
              d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>
    </td>
  </tr>
</template>

<script>
import RowButton from "@/Components/Table/RowButton.vue";
import { formatCurrency } from "@/utilities";

export default {
  components: {
    RowButton,
  },
  props: ["transaction"],
  emits: ["deleteTransaction"],
  methods: {
    formatCurrency,
  },
  computed: {
    date() {
      return this.transaction.date.format("ddd DD-MM-YYYY");
    },
    time() {
      return this.transaction.date.format("hh:mm a");
    },
    dateFromNow() {
      return this.transaction.date.fromNow();
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
    secondaryClass() {
      return {
        "text-gray-400": !this.transaction.transfer,
        "text-green-400": this.transaction.transfer && this.transaction.amount > 0,
        "text-orange-300": this.transaction.transfer && this.transaction.amount < 0,
      };
    },
    mainClass() {
      return {
        "text-gray-800": !this.transaction.transfer,
        "text-green-500": this.transaction.transfer && this.transaction.amount > 0,
        "text-orange-500": this.transaction.transfer && this.transaction.amount < 0,
      };
    },
  },
};
</script>