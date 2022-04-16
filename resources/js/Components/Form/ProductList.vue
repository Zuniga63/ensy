<template>
  <div class="relative w-full">
    <input
      type="text"
      class="
        block
        border-gray-200
        focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
        rounded-md
        shadow-sm
        w-full
      "
      :value="modelValue"
      @input="input"
      @focus="hasFocus = true"
      @blur="hasFocus = false"
      v-bind="$attrs"
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
          v-for="item in list"
          :key="item.id"
          class="hover:cursor-pointer w-full p-2 hover:bg-indigo-500 hover:text-white transition-colors"
          @click.stop="selectProduct(item)"
        >
          <div class="flex items-center">
            <!-- Imagen del cliente -->
            <div class="w-10 h-10 rounded-full overflow-hidden mr-2">
              <!-- <img :src="customer.image_url" :alt="customer.full_name" class="block w-full" /> -->
            </div>

            <div class="flex-grow">
              <p class="text-gray-800 text-sm font-medium">
                {{ item.name }}
              </p>
            </div>
          </div>
        </li>
      </ul>
    </transition>

    <div class="absolute top-0 right-2 h-full" v-show="product">
      <div class="flex flex-col h-full justify-center">
        <button class="rounded text-gray-800 hover:text-opacity-70 transition-opacity" @click="deselectProduct">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
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

import { normalizeString } from "@/utilities";

export default {
  props: {
    modelValue: {
      type: String,
      default: null,
    },
    products: {
      type: Array,
      default: [],
    },
    product: Object,
  },
  inheritAttrs: false,
  emits: ["update:modelValue", "selectProduct", "deselectProduct"],
  data() {
    return {
      hasFocus: false,
      productSelected: this.product,
    };
  },
  methods: {
    input(event) {
      this.$emit("update:modelValue", event.target.value);
    },
    selectProduct(product) {
      this.focus();
      this.$emit("update:modelValue", product.name);
      this.$emit("selectProduct", { product });
    },
    deselectProduct() {
      this.$emit("update:modelValue", null);
      this.$emit("deselectProduct");
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
  }, //.end methods
  computed: {
    /**
     * Establece si se despliega el listado de clientes
     */
    showList() {
      return !this.product && this.hasFocus;
    },
    /**
     * Se encarga de filtrar los clientes segun el campo se busqueda
     */
    list() {
      let search = normalizeString(this.modelValue);

      if (search) {
        return this.products.filter((item) => {
          /**
           * @type {string|null}
           */
          let name = normalizeString(item.name);
          return name.includes(search);
        });
      }

      return this.products;
    },
  }, //.end computed
};
</script>