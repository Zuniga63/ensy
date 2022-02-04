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
                  'bg-red-200 bg-opacity-20': box.balanceIsWrong,
                }"
                class="hover:bg-indigo-50"
              >
                <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                  <div class="flex flex-col">
                    {{ box.name }}
                    <span v-if="box.code" class="text-xs text-gray-400"
                      >Codigo: {{ box.code }}
                    </span>
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
                    justify-end
                    px-6
                    py-4
                    whitespace-nowrap
                    text-gray-800
                  "
                >
                  <!-- Link for show Transactions -->
                  <Link
                    :href="route('cashbox.show', box.slug)"
                    class="
                      p-2
                      mr-2
                      border border-gray-200
                      rounded
                      bg-white
                      hover:border-gray-400
                      hover:ring
                      hover:ring-gray-500
                      hover:ring-opacity-30
                      text-gray-800
                      hover:text-opacity-80
                    "
                    title="Ver Transacciones"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                      />
                    </svg>
                  </Link>
                  <!-- Link for Edit -->
                  <Link
                    :href="route('cashbox.edit', box.slug)"
                    :title="'Actualizar ' + box.name"
                    class="
                      p-2
                      border border-green-400
                      rounded
                      hover:bg-green-50
                      hover:ring
                      hover:ring-green-400
                      hover:ring-opacity-40
                      bg-white
                      text-green-500
                    "
                    :class="{ 'mr-2': box.balance === 0 }"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                      />
                      <path
                        fill-rule="evenodd"
                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </Link>

                  <!-- Link for delete box -->
                  <a
                    href="javascript:;"
                    :title="'Eliminar Caja ' + box.name"
                    class="
                      p-2
                      border border-red-400
                      rounded
                      bg-white
                      hover:bg-red-50
                      hover:ring
                      hover:ring-red-400
                      hover:ring-opacity-40
                      text-red-500
                    "
                    @click="$emit('deleteBox', box.id)"
                    v-if="box.balance === 0"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                      />
                    </svg>
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
                      border border-white
                      hover:border-gray-700
                      rounded-md
                      bg-gray-700
                      hover:bg-transparent
                      text-white
                      hover:text-gray-700
                      text-sm text-center
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
  emits: ["deleteBox"],
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