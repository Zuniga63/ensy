<template>
  <div
    class="relative pt-4 px-4 pb-2 border border-gray-200 rounded-md bg-gray-50"
  >
    <!-- Pill -->
    <span
      class="
        absolute
        top-0
        left-4
        px-2
        border border-gray-200
        rounded-full
        bg-white
        text-xs text-gray-800
        -translate-y-1/2
      "
      >Agregar Item</span
    >
    <div class="flex">
      <div class="flex-grow grid grid-cols-12 gap-2 mr-2">
        <!-- Description -->
        <div class="col-span-4">
          <jet-input
            type="text"
            class="w-full text-xs text-gray-800"
            :class="{
              'ring-2 ring-red-400': description.error,
            }"
            placeholder="Descripción"
            v-model.trim="description.value"
            @blur="description.error ? validateDescription() : null"
            ref="description"
            @keydown.enter="addItem"
          />
        </div>
        <!-- Cantidad -->
        <div class="col-span-2">
          <jet-input
            type="number"
            class="w-full text-xs text-gray-800 text-center"
            :class="{
              'ring-2 ring-red-400': quantity.error,
            }"
            placeholder="Cant."
            @change="quantity.error ? validateQuantity() : null"
            v-model.number="quantity.value"
            min="1"
            @keydown.enter="addItem"
          />
        </div>
        <!-- Valor Unitario -->
        <div class="col-span-2">
          <currency-input
            type="text"
            class="w-full text-xs text-gray-800 text-right"
            :class="{
              italic: onlyAmountChange,
              'ring-2 ring-red-400': unitValue.error,
            }"
            placeholder="Vlr. Unitario"
            v-model="unitValue.value"
            @focus="unitValue.focus = true"
            @blur="unitValueBlur"
            :disabled="unitValueDisabled"
            @keydown.enter="addItem"
          />
        </div>

        <!-- Descuento unitario -->
        <div class="col-span-2">
          <currency-input
            type="text"
            class="w-full text-xs text-gray-800 text-right"
            :class="{
              'ring-2 ring-red-400': discount.error,
            }"
            placeholder="Descuento"
            v-model="discount.value"
            @change="discount.error ? validateDiscount() : null"
            @keydown.enter="addItem"
          />
        </div>

        <!-- Importe -->
        <div class="col-span-2">
          <currency-input
            type="text"
            class="w-full text-xs text-gray-800 text-right"
            :class="{
              italic: onlyUnitValueChange,
              'ring-2 ring-red-400': amount.error,
            }"
            placeholder="Importe"
            v-model="amount.value"
            @focus="amount.focus = true"
            @blur="amountBlur"
            :disabled="amountDisabled"
            @keydown.enter="addItem"
          />
        </div>
      </div>

      <!-- Button -->
      <button
        class="
          justify-self-center
          self-center
          p-2
          rounded-full
          text-gray-800
          hover:bg-emerald-500 hover:text-white hover:shadow
          focus:outline-none focus:bg-emerald-500 focus:text-white
          transition-colors
        "
        @click="addItem"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 4v16m8-8H4"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
import CustomLabel from "@/Components/Form/Label.vue";
import JetInput from "@/Jetstream/Input.vue";
import CurrencyInput from "@/Components/CurrencyInput.vue";

/**
 * Clase personalizada para gestionar los inputs
 */
class input {
  /**
   * @constructor
   * @param {*} value is the value for default of input
   */
  constructor(value = null) {
    this.value = value;
    this.focus = false;
    this.error = false;
    this.errorMessage = null;
    this.default = value;
  }

  reset() {
    this.value = this.default;
    this.focus = false;
    this.error = false;
    this.errorMessage = null;
  }
}

class Item {
  constructor(description, quantity, unitValue) {
    this.description = description;
    this.quantity = quantity;
    this.unitValue = unitValue;
    this.discount = 0;
    this.discountPercentage = 0;
    this.amount = _.round(quantity * unitValue, 2);
    this.error = false;
    this.errors = {
      description: false,
      quantity: false,
      unitValue: false,
      discount: false,
      amount: false,
    };
  }

  addDiscount(discount) {
    this.discount = discount;
    this.discountPercentage = (this.discount / this.unitValue) * 100;
    this.discountPercentage = _.round(this.discountPercentage, 2);
    this.amount = (this.unitValue - this.discount) * this.quantity;
    this.amount = _.round(this.amount);
  }

  addError(property) {
    if (Object.hasOwnProperty.call(this.errors, property)) {
      this.errors[property] = true;
    }
  }

  clearErrors() {
    this.error = false;
    this.errors.quantity = false;
    this.errors.description = false;
    this.errors.unitValue = false;
    this.errors.discount = false;
    this.errors.amount = false;
  }
}

export default {
  components: {
    CustomLabel,
    JetInput,
    CurrencyInput,
  },
  emits: ["addItem"],
  data() {
    return {
      description: new input(null),
      quantity: new input(null),
      unitValue: new input(0),
      onlyUnitValueChange: false,
      discount: new input(null),
      amount: new input(0),
      onlyAmountChange: false,
    };
  },
  methods: {
    unitValueBlur() {
      this.unitValue.focus = false;
      this.onlyUnitValueChange =
        this.unitValue.value && this.unitValue.value > 0;
      this.unitValue.error ? this.validateUnitValue() : null;
    },
    amountBlur() {
      this.amount.focus = false;
      this.onlyAmountChange = this.amount.value && this.amount.value > 0;
      this.amount.error ? this.validateAmount() : null;
    },
    /**
     * Verifica si la descripción tiene valores valido
     * @return {Boolean}
     */
    validateDescription() {
      let ok = this.description.value && this.description.value.length > 0;
      this.description.error = !ok;

      return ok;
    },
    /**
     * Verify that the quantity has value and greater than 0
     * @return {Boolean}
     */
    validateQuantity() {
      let ok = this.quantity.value && this.quantity.value > 0;
      this.quantity.error = !ok;

      return ok;
    },
    /**
     * Verify that the unit value field has value and greather than 0
     * @return {Boolean}
     */
    validateUnitValue() {
      let ok = this.unitValue.value && this.unitValue.value > 0;
      this.unitValue.error = !ok;
      return ok;
    },
    validateDiscount() {
      let ok = true;
      if (
        this.unitValue.value &&
        this.unitValue.value > 0 &&
        this.discount.value &&
        this.discount.value > 0
      ) {
        ok = this.unitValue.value > this.discount.value;
      }

      this.discount.error = !ok;

      return ok;
    },
    /**
     * Verify that the amount field has value and greater than 0
     * @return {Boolean}
     */
    validateAmount() {
      let ok = this.amount.value && this.amount.value > 0;
      this.amount.error = !ok;
      return ok;
    },
    validate() {
      let ok = this.validateDescription();
      ok = this.validateQuantity();
      ok = this.validateUnitValue();
      ok = this.validateAmount();
      ok = this.validateDiscount();

      return ok;
    },
    getData() {
      let item = new Item(
        this.description.value,
        this.quantity.value,
        this.unitValue.value
      );

      if (this.discount.value && this.discount.value > 0) {
        item.addDiscount(this.discount.value);
      }

      return item;
    },
    reset() {
      this.$refs.description.focus();
      this.onlyUnitValueChange = false;
      this.onlyAmountChange = false;

      this.description.reset();
      this.quantity.reset();
      this.unitValue.reset();
      this.discount.reset();
      this.amount.reset();
    },
    addItem() {
      if (this.validate()) {
        this.$emit("addItem", this.getData());
        this.reset();
      }
    },
  },
  computed: {
    /**
     * Defines if the unit value has disabled considering the amount field.
     */
    unitValueDisabled() {
      return (
        this.onlyAmountChange && this.amount.value && this.amount.value > 0
      );
    },
    /**
     * Defines if the amount field has disabled considering the unit value field.
     */
    amountDisabled() {
      return (
        this.onlyUnitValueChange &&
        this.unitValue.value &&
        this.unitValue.value > 0
      );
    },
  },
  watch: {
    /**
     * Calculates the amount or unit value when the quantity changes.
     */
    "quantity.value"(newQuantity) {
      if (this.onlyUnitValueChange || this.unitValue.focus) {
        this.amount.value = this.unitValue.value * newQuantity;
      } else if (this.onlyAmountChange || this.amount.focus) {
        this.unitValue.value =
          newQuantity > 0 ? _.round(this.amount.value / newQuantity, 2) : 0;
      }
    },
    /**
     * Calculates the amount where the unique value was changed
     */
    "unitValue.value"(newValue) {
      if (this.onlyUnitValueChange || this.unitValue.focus) {
        this.amount.value = newValue * this.quantity.value;
        //this.validateAmount();
      }
    },
    /**
     * Calculates the unit value where amount was changed.
     */
    "amount.value"(newAmount) {
      if (this.onlyAmountChange || this.amount.focus) {
        this.unitValue.value =
          this.quantity.value > 0
            ? _.round(newAmount / this.quantity.value)
            : 0;
        //this.validateUnitValue();
      }
    },
  },
};
</script>