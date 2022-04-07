<template>
  <header class="grid grid-cols-4 gap-4 items-center px-6 pt-3 mb-2 bg-gray-100">
    <!-- Logo e Información -->
    <div class="col-span-3">
      <div class="flex items-center py-2">
        <!-- Logo -->
        <div class="w-16 h-16">
          <img
            :src="config.logo"
            :alt="config.name"
            class="block w-full h-full object-scale-down"
          />
        </div>

        <div class="flex-grow">
          <!-- Business Name -->
          <h2 class="text-lg font-semibold text-center uppercase text-gray-800 italic">
            {{ config.name }}
          </h2>
          <!-- NIT -->
          <p class="text-xs text-gray-800 text-center" v-if="config.nit">
            Nit:
            <span class="font-medium italic tracking-wider">{{
              config.nit
            }}</span>
          </p>

          <p class="text-xs text-gray-800 text-center" v-if="config.nit">
            Vendedor:
            <span class="font-medium italic tracking-wider">{{
              config.seller
            }}</span>
          </p>

          <!-- Address -->
          <p
            class="hidden text-xs text-gray-800 text-center"
            v-if="config.address.show"
          >
            {{ config.address.value }}
          </p>
          <!-- Email -->
          <p class="hidden text-xs text-gray-800 text-center" v-if="config.email.show">
            {{ config.email.value }}
          </p>
          <!-- Phone and Whatapp -->
          <p class="hidden text-xs text-gray-800 text-center" v-if="config.phone.show">
            <span>Tel.: {{ config.phone.number }}</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Numero de factura -->
    <div class="flex flex-col items-center">
      <h2 class="uppercase text-gray-800">Factura de venta</h2>
      <p class="text-red-500 text-lg tracking-widest">{{ config.invoiceNumber }}</p>
    </div>
  </header>
</template>

<script>
export default {
  props:['config'],
  /* data() {
    return {
      config: this.$page.props.businessConfig,
    };
  }, */
};
</script>