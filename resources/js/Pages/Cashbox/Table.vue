<template>
  <div class="flex flex-col">
    <div class="overflow-x-auto">
      <div class="align-middle inline-block w-full">
        <div class="shadow overflow-hidden border-b border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <!-- Head -->
            <thead class="bg-gray-50">
              <!-- Nanme -->
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  text-left text-sm text-gray-500
                  uppercase
                  tracking-widest
                "
              >
                Nombre
              </th>
              <!-- Base -->
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  text-center text-sm text-gray-500
                  uppercase
                  tracking-widest
                "
              >
                Base
              </th>
              <!-- Incomes -->
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  text-center text-sm text-gray-500
                  uppercase
                  tracking-widest
                "
              >
                Ingresos
              </th>
              <!-- Expenses -->
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  text-center text-sm text-gray-500
                  uppercase
                  tracking-widest
                "
              >
                Egresos
              </th>
              <!-- Balance -->
              <th
                scope="col"
                class="
                  px-6
                  py-3
                  text-center text-sm text-gray-500
                  uppercase
                  tracking-widest
                "
              >
                Saldo
              </th>
              <!-- Actions -->
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Edit</span>
              </th>
            </thead>

            <!-- Body -->
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="box in boxs"
                :key="box.id"
                :class="{
                  'bg-red-300 bg-opacity-20': box.balanceIsWrong,
                }"
                class="hover:bg-gray-200"
              >
                <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                  <div class="flex flex-col">
                    {{ box.name }}
                    <span v-if="box.code" class="text-xs text-gray-400">Codigo: {{box.code}} </span>
                  </div>
                </td>
                <td
                  class="
                    flex flex-col
                    px-6
                    py-4
                    whitespace-nowrap
                    text-gray-800 text-right
                  "
                >
                  <p>
                    {{ formatCurrency(box.base) }}
                  </p>
                  <p v-if="box.lastClosure" class="-mt-1 text-sm text-gray-400">
                    {{ box.lastClosureFromNow }}
                  </p>
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-gray-800 text-right"
                >
                  {{ formatCurrency(box.incomes) }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-gray-800 text-right"
                >
                  {{ formatCurrency(box.expenses) }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-gray-800 text-right"
                >
                  <div class="flex flex-col">
                    <span
                      :class="{
                        'text-red-800': box.balanceIsWrong,
                      }"
                    >
                      {{ formatCurrency(box.balance) }}
                    </span>
                    <span
                      v-if="box.balanceIsWrong"
                      class="text-sm text-red-800 text-opacity-90 -mt-1"
                    >
                      Existen inconsistencias.
                    </span>
                  </div>
                </td>
                <td
                  class="
                    flex
                    justify-around
                    px-6
                    py-4
                    whitespace-nowrap
                    text-gray-800
                  "
                >
                  <Link :href="route('cashbox.edit', box.slug)" :title="'Actualizar ' +  box.name " class="p-2 text-gray-800 hover:text-opacity-80"> 
                    <i class="fas fa-edit"></i> 
                  </Link>
                  <Link href="/"> <i class="fas fa-book"></i> </Link>
                  <a href="javascript:;" :title="'Eliminar Caja ' +  box.name " class="p-2 text-red-800 hover:text-opacity-80" @click="$emit('deleteBox', box.id)">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>

              <tr class="bg-gray-50">
                <td colspan="2" class="relative px-6 py-3">
                  <span class="sr-only">Nothing</span>
                </td>
                <td
                  class="px-6 py-3 text-right text-sm text-gray-500 font-bold"
                >
                  {{ incomes }}
                </td>
                <td
                  class="px-6 py-3 text-right text-sm text-gray-500 font-bold"
                >
                  {{ expenses }}
                </td>
                <td
                  class="px-6 py-3 text-right text-sm text-gray-500 font-bold"
                >
                  {{ balance }}
                </td>
                <td
                  class="px-6 py-3 text-right text-sm text-gray-500 font-bold"
                >
                  <Link
                    :href="route('cashbox.create')"
                    class="
                      inline-block
                      w-full
                      px-4
                      py-2
                      border border-white hover:border-gray-700
                      rounded-md
                      bg-gray-700
                      hover:bg-transparent
                      text-white hover:text-gray-700 text-sm text-center
                      tracking-wider
                      uppercase
                      font-bold
                    "
                  >
                    Crear Caja
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";

export default {
  components: {
    JetButton,
    Link,
  },
  props: ["boxs"],
  emits:['deleteBox'],
  setup(props) {
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
  },
  computed: {
    incomes() {
      // return this.boxs.reduce((prev, current) => prev + current.incomes, 0)
      return this.formatCurrency(
        this.boxs.reduce((prev, current) => prev + current.incomes, 0)
      );
    },
    expenses() {
      return this.formatCurrency(
        this.boxs.reduce((prev, current) => prev + current.expenses, 0)
      );
    },
    balance() {
      return this.formatCurrency(
        this.boxs.reduce((prev, current) => prev + current.balance, 0)
      );
    },
  },
};
</script>