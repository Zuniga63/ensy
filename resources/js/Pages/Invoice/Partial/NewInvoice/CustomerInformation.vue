<template>
  <div
    class="
      grid grid-cols-6
      gap-2
      items-center
      h-full
      p-3
      border border-gray-200
      rounded-md
      bg-gray-50
      shadow
    "
  >
    <!-- Name -->
    <div class="col-span-4">
      <div class="flex items-center">
        <custom-label
          for="customer-name"
          class="mr-2 text-sm text-gray-800"
          value="Cliente"
        />
        <div class="flex-grow w-full">
          <customer-list
            name="customer-name"
            id="customer-name"
            class="px-4 py-2 text-xs text-gray-800"
            placeholder="Selecciona un cliente."
            :customers="customers"
            v-model="customer.fullName"
            @select-customer="customerSelected"
            @deselect-customer="reset"
          />
          <input-error :message="errors.customerName" class="mt-1 text-xs" />
          <input-error :message="errors.customerId" class="mt-1 text-xs" />
        </div>
      </div>
    </div>

    <!-- Nit -->
    <div class="col-span-2">
      <div class="flex items-center">
        <custom-label
          for="customer-nit"
          class="mr-2 text-sm text-gray-800 tracking-wider"
          >Nit</custom-label
        >
        <div class="flex-grow w-full">
          <jet-input
            type="text"
            name="customer-nit"
            id="customer-nit"
            class="w-full text-xs text-center text-gray-800"
            placeholder="Escribelo aquí."
            v-model="customer.nit"
            :disabled="customer.customer ? true : false"
          />
          <input-error
            :message="errors.customerDocument"
            class="mt-1 text-xs"
          />
        </div>
      </div>
    </div>

    <!-- Address -->
    <div class="col-span-4">
      <div class="flex items-center">
        <custom-label
          for="customer-address"
          class="mr-2 text-sm text-gray-800"
          >Dirección</custom-label
        >
        <div class="flex-grow w-full">
          <jet-input
            type="text"
            name="customer-address"
            id="customer-address"
            class="w-full text-xs text-gray-800"
            placeholder="Escribe la dirección."
            v-model="customer.address"
          />
          <input-error :message="errors.customerAddress" class="mt-1 text-xs" />
        </div>
      </div>
    </div>

    <!-- Phone -->
    <div class="col-span-2">
      <div class="flex items-center">
        <custom-label for="customer-phone" class="mr-2 text-sm text-gray-800"
          >Telefono</custom-label
        >
        <div class="flex-grow w-full">
          <jet-input
            type="text"
            name="customer-phone"
            id="customer-phone"
            class="flex-grow w-full text-xs text-center text-gray-800"
            placeholder="Escribelo aquí."
            v-model="customer.phone"
          />
          <input-error :message="errors.customerPhone" class="mt-1 text-xs" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CustomerList from "@/Components/Form/CustomerList.vue";
import JetInput from "@/Jetstream/Input.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import InputError from "@/Components/Form/InputError.vue";

export default {
  components: {
    CustomerList,
    JetInput,
    CustomLabel,
    InputError,
  },
  props: ["customer", "customers", "errors"], //.end props
  methods: {
    /**
     * Actualiza la información de los campos del cliente
     * cuando este es seleccionado por el usuario.
     */
    customerSelected(customer) {
      this.customer.customer = customer;
      this.customer.nit = customer.document_number;
      this.customer.phone =
        customer.contacts && customer.contacts.length > 0
          ? (this.customer.phone = customer.contacts[0].number)
          : null;
    },
    /**
     * Resetea los valores de los campos cuando se
     * ha deseleccionado el cliente en la lista.
     * *El campo del nombre se resetea automaticamente.
     */
    reset() {
      this.customer.customer = null;
      this.customer.nit = null;
      this.customer.phone = null;
      this.customer.address = null;
    },
  }, //.end methods
};
</script>