<template>
  <input
    class="
      border-gray-300
      focus:border-indigo-300
      focus:ring
      focus:ring-indigo-200
      focus:ring-opacity-50
      rounded-md
      shadow-sm
    "
    @focus="$event.target.select()"
    @input="input"
    ref="input"
  />
</template>

<script>
export default {
  props: ["modelValue"],
  emits: ["update:modelValue"],
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
    focus() {
      this.$refs.input.focus();
    },
    input(event) {
      //Se elimina el formato del numero
      let value = this.deleteFormat(event.target.value);
      //Se pinta en pantalla
      event.target.value = value ? this.formatCurrency(value) : null;
      this.$emit("update:modelValue", value ? value : null);
    },
    formatCurrency(number) {
      return this.formater.format(number);
    },
    deleteFormat(text) {
      let value = text.replace("$", "");
      value = value.split(".");
      value = value.join("");

      value = parseFloat(value);

      return isNaN(value) ? 0 : value;
    },
    printModelValue() {
      this.$refs.input.value = this.modelValue
        ? this.formatCurrency(this.modelValue)
        : null;
    },
  },
  mounted() {
    this.printModelValue();
  },
  beforeUpdate() {
    this.printModelValue();
  },
};
</script>