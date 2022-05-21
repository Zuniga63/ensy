<template>
  <div class="px-2 py-1 border border-gray-200 rounded bg-gradient-to-b from-gray-50 to-gray-200 shadow">
    <h2
      class="
        mb-2
        border-b-4 border-gray-800 border-double
        text-sm text-center text-gray-800
        font-semibold
        tracking-wide
      "
    >
      Resumen
    </h2>
    <!-- Subtotal -->
    <sumary-item title="Subtotal" :amount="formatCurrency(sumary.subtotal)" class="mb-1" />
    <!-- Descuentos -->
    <sumary-item title="Descuentos" :amount="formatCurrency(sumary.discount)" v-if="sumary.discount" class="mb-1" />
    <!-- Total Factura -->
    <sumary-item title="Total Factura" :amount="formatCurrency(sumary.total)" class="mb-1" />
    <!-- Pago en efectivo -->
    <sumary-item
      title="Pago en Efectivo"
      :amount="formatCurrency(sumary.cash)"
      v-if="sumary.cash && !onlyInvoice"
      class="mb-1"
    />
    <!-- Financiado a credito -->
    <sumary-item
      title="Crédito"
      :amount="formatCurrency(sumary.credit)"
      v-if="sumary.credit && !onlyInvoice"
      class="mb-1"
    />
    <!-- Vueltos -->
    <sumary-item
      title="Vueltos"
      :amount="formatCurrency(sumary.change)"
      v-if="sumary.change && !onlyInvoice"
      class="mb-1"
    />

    <!-- Balance -->
    <sumary-item
      title="Saldo"
      :amount="formatCurrency(sumary.balance)"
      v-if="sumary.balance && !onlyInvoice"
      class="mb-1"
    />
  </div>
</template>
<script>
import SumaryItem from "./SumaryItem.vue";
export default {
  components: { SumaryItem },
  props: {
    sumary: Object,
    onlyInvoice: Boolean,
  },
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