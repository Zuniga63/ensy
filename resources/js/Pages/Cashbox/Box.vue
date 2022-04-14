<template>
  <div class="mb-5 border border-gray-400 rounded shadow overflow-hidden">
    <!-- Header -->
    <header class="flex justify-between items-center bg-gray-300 p-2 border-b border-gray-400 shadow">
      <div class="flex flex-col">
        <h2 class="text-base text-gray-800 font-bold">
          {{ box.name }}
        </h2>
        <p class="text-xs text-gray-600">
          Base:
          <span class="inline-block mr-2 tracking-wider font-bold">
            {{ formatCurrency(box.base) }}
          </span>
          <span v-if="box.lastClosure">
            {{ box.lastClosureFromNow }}
          </span>
        </p>
      </div>

      <Link :href="route('cashbox.edit', box.slug)" class="p-1 text-xl text-gray-800" title="Editar Caja">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 font-bold"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
          />
        </svg>
      </Link>
    </header>
    <!-- /.end header -->

    <!-- Body -->
    <div class="grid grid-cols-2 p-2">
      <!-- Incomes -->
      <div class="flex flex-col px-2 text-sm border-r border-gray-200">
        <p class="text-green-500">Ingresos</p>
        <p class="text-right text-green-600 tracking-wider font-bold">
          {{ formatCurrency(box.incomes) }}
        </p>
      </div>

      <!-- Expenses -->
      <div class="flex flex-col px-2 text-sm">
        <p class="text-red-500">Egresos</p>
        <p class="text-right text-red-600 tracking-wider font-bold">
          {{ formatCurrency(box.expenses) }}
        </p>
      </div>

      <!-- Balance -->
      <div class="flex flex-col col-span-2 px-2 border-t border-gray-200 text-gray-800">
        <p class="text-sm">Saldo</p>
        <p
          class="font-bold text-lg tracking-widest text-center"
          :class="{
            'text-red-600 underline': box.balanceIsWrong,
          }"
        >
          {{ formatCurrency(box.balance) }}
        </p>
        <p v-if="box.balanceIsWrong" class="text-red-800 text-sm text-center">Existen inconsistencias en los datos.</p>
      </div>
    </div>
    <!-- /.end body -->

    <!-- Footer -->
    <footer class="flex justify-between p-2 bg-gray-300">
      <jet-button type="button" class="text-sm" @click="goToBox">Transacciones</jet-button>
      <jet-danger-button
        type="button"
        class="text-sm"
        v-if="box.balance === 0"
        @click="this.$emit('deleteBox', this.box?.id)"
        >Eliminar</jet-danger-button
      >
    </footer>
    <!-- /.end footer -->
  </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import { Inertia } from "@inertiajs/inertia";
import { formatCurrency } from "@/utilities";

export default {
  components: {
    JetButton,
    JetDangerButton,
    Link,
  },
  props: ["box"],
  emits: ["deleteBox"],
  methods: {
    formatCurrency,
    goToBox() {
      Inertia.get(route("cashbox.show", this.box.slug));
    },
  },
};
</script>