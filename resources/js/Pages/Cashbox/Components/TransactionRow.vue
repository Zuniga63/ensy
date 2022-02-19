<template>
  <tr class="hover:bg-indigo-50">
    <!-- ID -->
    <td class="px-3 py-2 text-center text-gray-400">{{ transaction.id }}</td>
    <!-- Date and Time -->
    <td class="px-3 py-2 text-left text-gray-800 whitespace-nowrap">
      <div>
        <p>{{ date }}</p>
        <p class="text-sm text-gray-800 text-opacity-80">
          {{ time }} - {{ dateFromNow }}
        </p>
      </div>
    </td>
    <!-- Description -->
    <td class="px-3 py-2 text-gray-800">
      <p :class="{ 'text-green-500': transaction.transfer }">
        {{ transaction.description }}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="
            inline-block
            h-6
            w-6
            ml-2
            p-1
            border border-green-400
            bg-green-50
            rounded-full
          "
          viewBox="0 0 20 20"
          fill="currentColor"
          v-if="transaction.transfer"
        >
          <path
            d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"
          />
        </svg>
      </p>
      <div class="text-gray-800 text-opacity-75 text-sm">
        <p>
          Creado: {{ createdAtFromNow }}
          <span v-if="!createIsSameUpdate">
            - Actualizado: {{ updatedAtFromNow }}
          </span>
        </p>
      </div>
    </td>
    <!-- Amount -->
    <td
      class="px-3 py-2 text-right"
      :class="{ 'text-red-500': !isAIncome, 'text-green-500': isAIncome }"
    >
      {{ formatCurrency(transaction.amount) }}
    </td>
    <!-- Balance -->
    <td class="px-3 py-2 text-gray-800 text-right">
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
        <row-button
          type="delete"
          title="Eliminar Transacción"
          @click="deleteTransaction"
        />
      </div>
      <!-- Blocked Icon -->
      <div class="flex justify-end" v-else>
        <div
          class="border border-gray-400 rounded p-2 bg-gray-50 text-gray-500"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
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
import Swal from "sweetalert2";
import { Inertia } from "@inertiajs/inertia";
import RowButton from "@/Components/Table/RowButton.vue";

export default {
  components: {
    RowButton,
  },
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
  methods: {
    formatCurrency(number) {
      return this.formater.format(number);
    },
    deleteTransaction() {
      Swal.fire({
        title: "¿Está seguro?",
        text: "Está acción no puede revertirse y eleminará la transacción de la base de datos.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Si, Eliminala!",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          this.destroy();
        }
      });
    },
    destroy() {
      let name = "cashbox.destroyTransaction";
      let parameters = [this.transaction.cashbox_id, this.transaction.id];
      let url = route(name, parameters);
      Inertia.delete(url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          let result = page.props.flash.message;
          let title = "¡Transacción Eliminada!";
          let message = result.message;
          let icon = "success";
          if (result.ok) {
            Swal.fire({
              title,
              icon,
              text: message,
            });
          } else {
            icon = "error";
            title = "¡Oops!";
            Swal.fire({
              icon,
              title,
              text: message,
            });
          }
        },
      });
    },
  },
  computed: {
    date() {
      return this.transaction.date.format("dddd, DD-MM-YYYY");
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
  },
};
</script>