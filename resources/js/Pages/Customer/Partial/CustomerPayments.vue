<template>
  <div class="max-h-screen min-h-[15rem] shadow border-b border-gray-300 overflow-y-auto overflow-x-auto">
    <table class="relative min-w-full table-auto">
      <thead class="sticky top-0 bg-gray-50">
        <tr>
          <!-- # -->
          <th scope="col" class="hidden lg:table-cell px-6 py-3 text-center text-gray-500 text-sm">#</th>
          <!-- Fecha-->
          <th
            scope="col"
            class="px-3 sm:px-6 py-3 text-center text-xs sm:text-sm text-gray-500 tracking-wider uppercase"
          >
            Fecha
          </th>
          <!-- Subtotal -->
          <th
            scope="col"
            class="hidden lg:table-cell px-6 py-3 text-center text-sm text-gray-500 tracking-wider uppercase"
          >
            Descripción
          </th>
          <!-- Valor -->
          <th
            scope="col"
            class="px-3 sm:px-6 py-3 text-center text-xs sm:text-sm text-gray-500 tracking-wider uppercase"
          >
            Importe
          </th>

          <th scope="col" class="hidden lg:table-cell relative px-6 py-3">
            <span class="sr-only">Actions</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(payment, index) in paymentList" :key="payment.id" class="transition-colors hover:bg-indigo-50">
          <!-- Index -->
          <td class="hidden lg:table-cell px-3 py-2 text-gray-800 text-center text-sm">
            {{ index + 1 }}
          </td>

          <!-- Date -->
          <td class="px-3 py-2 text-gray-800 text-xs lg:text-sm text-center">
            <span class="lg:hidden"> {{ formatDate(payment.payment_date, true) }} </span>
            <span class="hidden lg:inline-block"> {{ formatDate(payment.payment_date) }} </span>
          </td>

          <td class="px-3 py-2 text-gray-800 text-xs lg:text-sm">
            <span v-if="groupBy === 'individual'"> {{ payment.description }} </span>
            <div v-else class="flex flex-col text-xs" v-for="(description, index) in payment.description" :key="index">
              <span> {{ description }} </span>
            </div>
          </td>

          <td class="hidden lg:table-cell px-3 py-2 text-gray-800 text-right text-xs sm:text-sm lg:text-base">
            {{ formatCurrency(payment.amount) }}
          </td>

          <!-- Acciones -->
          <td class="hidden lg:table-cell px-3 py-2">
            <!-- <div class="flex justify-end">
              <row-button type="show" class="mr-2" :href="route('customer.show', customer.id)" title="Ver Cliente" />
              <row-button type="edit" class="mr-2" title="Editar Cliente" :href="route('customer.edit', customer.id)" />
              <row-button type="delete" @click="deleteCustomer(customer)" title="Eliminar Cliente" />
            </div> -->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { formatCurrency } from "@/utilities";
import dayjs from "dayjs";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import isSameOrBefore from "dayjs/plugin/isSameOrBefore";
export default {
  props: ["payments", "groupBy"],
  setup(props) {
    dayjs.extend(isSameOrAfter);
    dayjs.extend(isSameOrBefore);
  },
  methods: {
    formatCurrency,
    formatDate(date, short = false) {
      date = dayjs(date);
      if (short) return date.format("DD-MM-YY");
      if (this.groupBy === "monthly") return date.format("MMMM YYYY");

      return date.format("DD-MM-YYYY");
    },
    /**
     * Se encarga de agrupar los pagos segun el tipo de
     * agrupamiento.
     * @param {array} list - listado de pagos
     */
    groupPayments(list) {
      let group = [];
      if (list.length > 0) {
        if (this.groupBy === "daily") {
          let date = dayjs(list[0].payment_date).startOf("day");
          let endDate = dayjs(list[list.length - 1].payment_date).endOf("day");

          while (date.isSameOrBefore(endDate)) {
            let startDay = date.clone().startOf("day");
            let endDay = date.clone().endOf("day");

            //Se filtran las transacciones
            let dailyPayments = list.filter((payment) => {
              let paymentDate = dayjs(payment.payment_date);
              return paymentDate.isSameOrAfter(startDay) && paymentDate.isSameOrBefore(endDay);
            });

            if (dailyPayments.length > 0) {
              let amount = 0;
              let description = [];

              dailyPayments.forEach((payment) => {
                amount += parseFloat(payment.amount);
                if (!description.includes(payment.description)) {
                  description.push(payment.description);
                }
              });

              group.push({
                payment_date: date,
                description,
                amount,
              });
            }

            date = date.add(1, "day");
          }
        } else if (this.groupBy === "monthly") {
          let date = dayjs(list[0].payment_date).startOf("month");
          let endDate = dayjs(list[list.length - 1].payment_date).endOf("month");

          while (date.isSameOrBefore(endDate)) {
            let startMonth = date.clone().startOf("month");
            let endMonth = date.clone().endOf("month");

            //Se filtran las transacciones
            let dailyPayments = list.filter((payment) => {
              let paymentDate = dayjs(payment.payment_date);
              return paymentDate.isSameOrAfter(startMonth) && paymentDate.isSameOrBefore(endMonth);
            });

            if (dailyPayments.length > 0) {
              let amount = 0;
              let description = [];

              dailyPayments.forEach((payment) => {
                amount += parseFloat(payment.amount);
                if (!description.includes(payment.description)) {
                  description.push(payment.description);
                }
              });

              group.push({
                payment_date: date,
                description,
                amount,
              });
            }

            date = date.add(1, "month");
          }
        } else {
          return list;
        }
      }

      return group;
    },
  }, //.end method
  computed: {
    paymentList() {
      let list = this.payments.slice();
      list = this.groupPayments(list);
      return list;
    },
  },
};
</script>
