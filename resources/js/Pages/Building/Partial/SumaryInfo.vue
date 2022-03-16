<template>
  <transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0 scale-90"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-90"
  >
    <div
      class="
        flex flex-col
        items-center
        px-4
        py-2
        border border-gray-200
        rounded-sm
        shadow
      "
      v-show="showSumary"
    >
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-90"
        enter-to-class="opacity-100 scale-100"
      >
        <p class="text-gray-800 text-xs line-clamp-1" v-show="owner">
          {{ owner?.full_name }}
        </p>
      </transition>

      <transition
        enter-active-class="transition ease-out duration-200 delay-100"
        enter-from-class="opacity-0 scale-90"
        enter-to-class="opacity-100 scale-100"
      >
        <p class="text-gray-600 text-xs min-h-[0.5rem]" v-show="owner">
          <span> {{ owner?.document_type }} </span>:
          <span>
            {{ formatDocument(owner?.document_number) }}
          </span>
        </p>
      </transition>
      <div class="self-stretch">
        <div class="flex justify-around">
          <p
            class="text-xs text-gray-500"
            v-show="insured && realEstateCommission !== realEstateUtility"
          >
            Comisión: {{ formatCurrency(realEstateCommission) }}
          </p>
          <p
            class="text-xs"
            :class="{
              'text-gray-800': realEstateUtility === 0,
              'text-green-500': realEstateUtility > 0,
              'text-red-500': realEstateUtility < 0,
            }"
          >
            Utilidad: {{ formatCurrency(realEstateUtility) }}
          </p>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  props: {
    owner: {
      type: Object,
      default: null,
    },
    leaseFee: {
      type: [Number, String],
      default: 0,
    },
    commission: {
      type: [Number, String],
      default: 0,
    },
    insured: {
      type: Boolean,
      default: false,
    },
    insuranceCommission: {
      type: [Number, String],
      default: 0,
    },
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
    /**
     * Se encarga de poner un punto cada tercer numero
     * de un documento
     * @param {string} document numero de documento
     * @return {string}
     */
    formatDocument(document) {
      if (document) {
        //Se recupera cada elemento de forma inversa
        /**@type {array} */
        let elements = document.split("").reverse();
        let result = [];

        elements.forEach((item, index) => {
          let count = index + 1;
          result.push(item);

          if (count % 3 === 0) result.push(".");
        });

        return result.reverse().join("");
      }
    },
  },
  computed: {
    commissionFraction() {
      let value = parseFloat(this.commission) / 100.0;

      if (!isNaN(value)) return value;

      return 0;
    },
    insuranceFraction() {
      let value = parseFloat(this.insuranceCommission) / 100.0;

      if (!isNaN(value)) return value;

      return 0;
    },
    /**
     * This method calculates the real estate commision
     */
    realEstateCommission() {
      let leaseFee = parseFloat(this.leaseFee);
      let discount = 0;
      if (!isNaN(leaseFee)) {
        discount = Math.round(leaseFee * this.commissionFraction);
      }

      return discount;
    },
    /**
     * This method calculates the utility of real state
     * depending of insurance commissión.
     */
    realEstateUtility() {
      let utility = this.realEstateCommission;

      if (this.insured) {
        let leaseFee = parseFloat(this.leaseFee);

        if (!isNaN(leaseFee)) {
          utility -= Math.round(leaseFee * this.insuranceFraction);
        }
      }

      return utility;
    },
    showSumary() {
      return (
        this.owner ||
        this.realEstateCommission !== 0 ||
        this.realEstateUtility !== 0
      );
    },
  },
};
</script>