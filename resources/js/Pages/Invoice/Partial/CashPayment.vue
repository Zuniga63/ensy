<template>
  <div class="relative flex items-start px-4 pt-6 pb-2 mb-4 border border-gray-200 rounded-md shadow">
    <!-- Pill -->
    <span
      class="
        absolute
        top-0
        left-4
        px-2
        py-1
        border border-gray-200
        rounded-full
        text-xs text-gray-800
        bg-gray-50
        -translate-y-1/2
      "
      >Registrar Pago</span
    >
    <!-- /.end pill -->

    <!-- Cashbox and Amount -->
    <div class="flex-grow grid grid-cols-2 gap-x-4 gap-y-2 items-start mr-4">
      <!-- Cashbox -->
      <div class="flex items-center">
        <custom-label for="invoiceCashbox" value="Caja" class="mr-2 text-sm text-gray-800" />

        <select
          name="invoiceCashbox"
          id="invoiceCashbox"
          v-model="box"
          class="
            block
            flex-grow
            w-full
            px-4
            py-2
            border border-gray-200
            rounded-md
            text-xs text-gray-800
            focus:border-indigo-400 focus:ring focus:ring-indigo-500 focus:ring-opacity-40
          "
          ref="input"
          @keydown.enter="add"
        >
          <option :value="null">Selecciona una caja</option>
          <option v-for="box in boxs" :value="box" :key="box.id">
            {{ box.name }}
          </option>
        </select>
      </div>

      <!-- Amount -->
      <div class="flex items-center">
        <custom-label for="invoiceCashboxAmount" value="Importe" class="mr-2 text-sm text-gray-800" />

        <currency-input
          name="invoiceCashboxAmount"
          id="invoiceCashboxAmount"
          type="text"
          placeholder="Escribelo aquí."
          v-model="amount"
          class="w-full text-xs text-gray-800 text-right"
          @keydown.enter="add"
        />
      </div>

      <div class="col-span-2">
        <div class="flex">
          <!-- Register Transaction -->
          <label class="flex items-center mr-3">
            <jet-checkbox name="registerTransaction" v-model:checked="registerTransaction" />
            <span class="ml-2 text-sm text-gray-600">Registrar Transacción</span>
          </label>

          <!-- Use for Change -->
          <label class="flex items-center mr-3">
            <jet-checkbox name="useForChange" v-model:checked="useForChange" />
            <span class="ml-2 text-sm text-gray-600">Usar para cambio</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Button -->
    <button
      class="
        justify-self-center
        p-2
        rounded-full
        text-gray-800
        hover:bg-emerald-500 hover:text-white hover:shadow
        focus:outline-none focus:bg-emerald-500 focus:text-white
        transition-colors
      "
      @click="add"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
      </svg>
    </button>
  </div>
</template>

<script>
import CustomLabel from "@/Components/Form/Label.vue";
import JetInput from "@/Jetstream/Input.vue";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";

export default {
  components: {
    CustomLabel,
    JetInput,
    CurrencyInput,
    JetCheckbox,
  },
  props: ["boxs", "sumary"],
  emits: ["addPayment"],
  data() {
    return {
      box: this.boxs.length > 0 ? this.boxs[0] : null,
      amount: 0,
      registerTransaction: true,
      useForChange: false,
    };
  },
  methods: {
    getData() {
      return {
        box: this.box,
        amount: this.amount,
        registerTransaction: this.registerTransaction,
        useForChange: this.useForChange,
        error: false,
        errors: {
          boxId: false,
          amount: false,
          registerTransaction: false,
          useForChange: false,
        },
      };
    },
    reset() {
      this.amount = 0;
      this.registerTransaction = true;
      this.useForChange = false;
      this.$refs.input.focus();
    },
    add() {
      if (this.box && this.amount > 0) {
        this.$emit("addPayment", this.getData());
        this.reset();
      }
    },
  },
  watch: {
    "sumary.credit"(amount) {
      if (amount > 0) {
        this.amount = this.sumary.credit;
      }
    },
  },
};
</script>