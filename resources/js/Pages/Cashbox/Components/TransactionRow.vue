<template>
  <tr class="hover:bg-indigo-50">
    <!-- ID -->
    <td class="px-3 py-2 text-center text-gray-400"> {{transaction.id}} </td>
    <!-- Date and Time -->
    <td class="px-3 py-2 text-left text-gray-800 whitespace-nowrap">
      <div>
        <p> {{ date}} {{time}} </p>
        <p class="text-sm text-gray-800 text-opacity-80"> {{ dateFromNow }} </p>
      </div>
    </td>
    <!-- Description -->
    <td class="px-3 py-2 text-gray-800 ">
      <p :class="{'text-green-500': transaction.transfer}">
        {{ transaction.description }}
        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-6 w-6 ml-2 p-1 border border-green-400 bg-green-50 rounded-full" viewBox="0 0 20 20" fill="currentColor" v-if="transaction.transfer">
          <path
            d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z" />
        </svg>
      </p>
      <div class="text-gray-800 text-opacity-75 text-sm">
        <p> Creado: {{ createdAtFromNow }} </p>
        <p v-if="!createIsSameUpdate"> Actualizado: {{ updatedAtFromNow }} </p>
      </div>
    </td>
    <!-- Amount -->
    <td class="px-3 py-2 text-right" :class="{'text-red-500': !isAIncome, 'text-green-500': isAIncome}">
      {{ formatCurrency(transaction.amount) }}
    </td>
    <!-- Balance -->
    <td class="px-3 py-2 text-gray-800 text-right">
      {{ formatCurrency(transaction.balance) }} </td>
    <td class="px-3 py-2">
      <div class="flex justify-end" v-if="!transaction.blocked">
        <!-- Edit -->
        <a href="javascript:;" class="border border-green-400 p-2 mr-2 hover:bg-green-100 rounded text-green-500" @click="$emit('updateTransaction', transaction)" v-if="!transaction.transfer">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
          </svg>
        </a>
        <!-- Delete -->
        <a href="javascript:;" class="border border-red-400 rounded p-2 hover:bg-red-100 text-red-500" @click="deleteTransaction">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
              clip-rule="evenodd" />
          </svg>
        </a>
      </div>

      <div class="flex justify-end" v-else>
        <div class="border border-gray-400 rounded p-2 bg-gray-50 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
          </svg>

        </div>
      </div>

    </td>
  </tr>
</template>

<script>
import Swal from "sweetalert2";
import { Inertia } from "@inertiajs/inertia";

export default {
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