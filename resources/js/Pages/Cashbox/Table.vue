<template>
  <div class="flex flex-col">
    <div class="overflow-x-auto">
      <div class="align-middle inline-block w-full">
        <div class="shadow overflow-x-auto border-b border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <!-- Head -->
            <thead class="bg-gray-50">
              <!-- Nanme -->
              <th scope="col" class="px-6 py-3 text-left text-sm text-gray-500 uppercase tracking-widest">Nombre</th>
              <!-- Base -->
              <th scope="col" class="px-6 py-3 text-center text-sm text-gray-500 uppercase tracking-widest">Base</th>
              <!-- Incomes -->
              <th scope="col" class="px-6 py-3 text-center text-sm text-gray-500 uppercase tracking-widest">
                Ingresos
              </th>
              <!-- Expenses -->
              <th scope="col" class="px-6 py-3 text-center text-sm text-gray-500 uppercase tracking-widest">Egresos</th>
              <!-- Balance -->
              <th scope="col" class="px-6 py-3 text-center text-sm text-gray-500 uppercase tracking-widest">Saldo</th>
              <!-- Acumulado -->
              <th scope="col" class="px-6 py-3 text-center text-sm text-gray-500 uppercase tracking-widest">
                Acumulado
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
                <!-- Nombre -->
                <td class="px-6 py-2 whitespace-nowrap text-gray-800">
                  <div class="flex flex-col">
                    {{ box.name }}
                    <span v-if="box.code" class="text-xs text-gray-400">Codigo: {{ box.code }} </span>
                  </div>
                </td>
                <!-- Base -->
                <td class="px-6 py-2 whitespace-nowrap text-gray-800 text-right">
                  <div class="flex flex-col">
                    <p>
                      {{ formatCurrency(box.base) }}
                    </p>
                    <p v-if="box.lastClosure" class="-mt-1 text-sm text-gray-400">
                      {{ box.lastClosureFromNow }}
                    </p>
                  </div>
                </td>
                <!-- Ingresos -->
                <td class="px-6 py-2 whitespace-nowrap text-right">
                  <div class="flex flex-col">
                    <span class="text-green-500">{{ formatCurrency(box.incomes) }}</span>
                    <div class="flex items-center justify-between text-indigo-500" v-if="box.deposits">
                      <switch-icon class="h-4 w-4" solid/>
                      <span class="text-sm">{{ formatCurrency(box.deposits) }}</span>
                    </div>
                  </div>
                </td>
                <!-- Expenses -->
                <td class="px-6 py-2 whitespace-nowrap text-right">
                  <div class="flex flex-col">
                    <span class="text-red-500">{{ formatCurrency(box.expenses) }}</span>
                    <div class="flex items-center justify-between text-orange-400" v-if="box.transfers">
                      <switch-icon class="h-4 w-4" solid/>
                      <span class="text-sm">{{ formatCurrency(box.transfers) }}</span>
                    </div>
                  </div>
                </td>
                <!-- Balance -->
                <td class="px-6 py-2 whitespace-nowrap text-gray-800 text-right">
                  <div class="flex flex-col">
                    <span
                      :class="{
                        'text-red-800': box.balanceIsWrong,
                      }"
                    >
                      {{ formatCurrency(box.balance) }}
                    </span>
                    <span v-if="box.balanceIsWrong" class="text-sm text-red-800 text-opacity-90 -mt-1">
                      Existen inconsistencias.
                    </span>
                  </div>
                </td>
                <!-- Acumulado -->
                <td class="px-6 py-2 whitespace-nowrap text-gray-800 text-right">
                  <div class="flex flex-col">
                    <span>
                      {{ formatCurrency(box.accumulated) }}
                    </span>
                  </div>
                </td>
                <!-- Actions -->
                <td class="px-6 py-2 whitespace-nowrap text-gray-800">
                  <div class="flex justify-end items-center">
                    <!-- Link for show Transactions -->
                    <row-button
                      :href="route('cashbox.show', box.slug)"
                      type="show"
                      class="mr-2"
                      :title="'Vizualizar ' + box.name"
                    />
                    <!-- Link for Edit -->
                    <row-button
                      :href="route('cashbox.edit', box.slug)"
                      type="edit"
                      :class="{ 'mr-2': box.balance === 0 }"
                      :title="'Actualizar ' + box.name"
                    />

                    <!-- Link for delete box -->
                    <row-button
                      type="delete"
                      :title="'Actualizar ' + box.name"
                      @click="$emit('deleteBox', box.id)"
                      v-if="box.balance === 0"
                    />
                  </div>
                </td>
              </tr>

              <!-- Sumary -->
              <tr class="bg-gray-50">
                <td colspan="2" class="relative px-6 py-3">
                  <span class="sr-only">Nothing</span>
                </td>
                <!-- Incomes -->
                <td class="px-6 py-2 text-right text-sm font-bold">
                  <div class="flex flex-col">
                    <span class="text-green-500">{{ incomes }}</span>
                    <div class="flex items-center justify-between text-indigo-500">
                      <switch-icon class="h-4 w-4" solid/>
                      <span class="text-sm">{{ deposits }}</span>
                    </div>
                  </div>
                </td>
                <!-- Expeneses -->
                <td class="px-6 py-2 text-right text-sm text-gray-500 font-bold">
                  <div class="flex flex-col">
                    <span class="text-red-500">{{ expenses }}</span>
                    <div class="flex items-center justify-between text-orange-500">
                      <switch-icon class="h-4 w-4" solid/>
                      <span class="text-sm">{{ transfers }}</span>
                    </div>
                  </div>
                </td>
                <!-- Balance -->
                <td colspan="2" class="px-6 py-2 text-center text-xl text-gray-500 font-bold">
                  {{ balance }}
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
import JetButton from "@/Jetstream/Button.vue";
import RowButton from "@/Components/Table/RowButton.vue";
import { formatCurrency } from "@/utilities";
import SwitchIcon from "@/Components/Svgs/SwitchHorizontal.vue";

export default {
  components: { JetButton, RowButton, SwitchIcon },
  props: ["boxs"],
  emits: ["deleteBox"],
  methods: {
    formatCurrency,
  },
  computed: {
    incomes() {
      // return this.boxs.reduce((prev, current) => prev + current.incomes, 0)
      return this.formatCurrency(this.boxs.reduce((prev, current) => prev + current.incomes, 0));
    },
    expenses() {
      return this.formatCurrency(this.boxs.reduce((prev, current) => prev + current.expenses, 0));
    },
    deposits() {
      return this.formatCurrency(this.boxs.reduce((prev, current) => prev + current.deposits, 0));
    },
    transfers() {
      return this.formatCurrency(this.boxs.reduce((prev, current) => prev + current.transfers, 0));
    },
    balance() {
      return this.formatCurrency(this.boxs.reduce((prev, current) => prev + current.balance, 0));
    },
  },
};
</script>