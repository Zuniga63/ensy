<template>
  <div class="relative w-full">
    <input
      type="text"
      class="
        block
        border-gray-200
        focus:border-indigo-300
        focus:ring
        focus:ring-indigo-200
        focus:ring-opacity-50
        rounded-md
        shadow-sm
        w-full
      "
      :value="modelValue"
      @input="input"
      @focus="hasFocus = true"
      @blur="hasFocus = false"
      v-bind="$attrs"
      :disabled="customerSelected ? true : false"
      ref="input"
    />

    <!-- Listado de clientes -->
    <transition
      name="customerList"
      enter-active-class="transition ease-out duration-200 origin-top"
      enter-from-class="opacity-0 scale-y-0"
      enter-to-class="opacity-100 scale-y-100"
      leave-active-class="transition ease-in duration-100 origin-top delay-100"
      leave-from-class="opacity-100 scale-y-100"
      leave-to-class="opacity-0 scale-y-0"
    >
      <ul
        class="
          absolute
          w-full
          max-h-40
          border border-gray-200
          rounded-b-md
          mt-1
          bg-white
          divide-y divide-slate-400
          overflow-y-auto
          z-40
        "
        v-show="showList"
      >
        <li
          v-for="customer in list"
          :key="customer.id"
          class="
            hover:cursor-pointer
            w-full
            p-2
            hover:bg-indigo-500 hover:text-white
            transition-colors
          "
          @click.stop="selectCustomer(customer)"
        >
          <div class="flex">
            <!-- Imagen del cliente -->
            <div class="w-10 rounded-full overflow-hidden mr-2">
              <img
                :src="customer.image_url"
                :alt="customer.full_name"
                class="block w-full"
              />
            </div>

            <div class="flex-grow">
              <p class="text-gray-800 text-sm font-medium">
                {{ customer.full_name }}
              </p>
              <p class="text-inherit text-opacity-40 text-xs">
                <span>{{ customer.document_type }}:</span>
                {{ formatDocument(customer.document_number) }}
              </p>
            </div>
          </div>
        </li>
      </ul>
    </transition>

    <div class="absolute top-0 right-2 h-full" v-show="customerSelected">
      <div class="flex flex-col h-full justify-center">
        <button
          class="rounded text-gray-800 hover:text-opacity-70 transition-opacity"
          @click="deselectCustomer"
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
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
/**
* @fileoverview Componente para el manejo de clientes
*
* @author Andrés Zuñiga
* @version 1.0
*/

export default {
  props: {
    modelValue: {
      type: String,
      default: null,
    },
    customers: {
      type: Array,
      default: [],
    },
  },
  inheritAttrs: false,
  emits: ["update:modelValue", 'selectCustomer', 'deselectCustomer'],
  data() {
    return {
      hasFocus: false,
      customerSelected: null,
    };
  },
  methods: {
    input(event) {
      this.$emit("update:modelValue", event.target.value);
    },
    selectCustomer(customer) {
      this.customerSelected = customer;
      this.focus();
      this.$emit("update:modelValue", customer.full_name);
      this.$emit("selectCustomer", customer);
    },
    deselectCustomer() {
      this.customerSelected = null;
      this.$emit("update:modelValue", null);
      this.$emit("deselectCustomer");
      setTimeout(() => {
        this.focus();
      }, 50);
    },
    /**
     * Permite otorgarle el foco al input
     */
    focus() {
      this.$refs.input.focus();
    },
    /**
     * Este metodo se encarga de llevar a minusculas el texto
     * pasado como parametro y remover de forma segura los guiños como
     * las eñes.
     * @param String text cadena de texto a normalizar.
     */
    normalizeString(text) {
      return text
        ? text
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
        : null;
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
    }, //.end formatDocument()
  }, //.end methods
  computed: {
    /**
     * Establece si se despliega el listado de clientes
     */
    showList() {
      return !this.customerSelected && this.hasFocus;
    },
    /**
     * Se encarga de filtrar los clientes segun el campo se busqueda
     */
    list() {
      let search = this.normalizeString(this.modelValue);

      if (search) {
        return this.customers.filter((item) => {
          /**
           * @type {string|null}
           */
          let name = this.normalizeString(item.full_name);
          return name.includes(search);
        });
      }

      return this.customers;
    },
  }, //.end computed
};
</script>