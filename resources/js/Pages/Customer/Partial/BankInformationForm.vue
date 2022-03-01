<template>
  <jet-form-section @submitted="submit">
    <template #title> Información Bancaria </template>

    <template #description>
      Guarda la información bancaria del cliente, como el nombre del banco,
      numero de cuenta, tipo de cuneta e información del titular.
    </template>

    <template #form>
      <!-- Nombres del banco -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="customer-bank-name" class="mb-2" value="Banco" />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-bank-name"
          class="w-full"
          placeholder="Escribe el nombre del banco"
          v-model="form.bank_name"
        />
        <jet-input-error :message="form.errors.bank_name" class="mt-2" />
      </div>

      <!-- Numero de cuenta-->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-bank-account-number"
          class="mb-2"
          value="Numero de cuenta"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-bank-account-number"
          class="w-full text-center"
          placeholder="Escribe el numero de cuenta."
          v-model="form.bank_account_number"
        />

        <!-- Mensaje de error -->
        <jet-input-error
          :message="form.errors.bank_account_number"
          class="mt-2"
        />
      </div>

      <!-- Titular de la cuenta -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-bank-account-holder"
          class="mb-2"
          value="Titular de la cuenta"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-bank-account-holder"
          class="w-full"
          placeholder="Escribe el numero de cuenta."
          v-model="form.bank_account_holder"
        />

        <!-- Mensaje de error -->
        <jet-input-error
          :message="form.errors.bank_account_holder"
          class="mt-2"
        />
      </div>

      <!-- Documento del titular -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-bank-account-holde-documentr"
          class="mb-2"
          value="Documento del titular"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-bank-account-holder-document"
          class="w-full text-center"
          placeholder="Escribe el numero de documento."
          v-model="form.bank_account_holder_document"
        />

        <!-- Mensaje de error -->
        <jet-input-error
          :message="form.errors.bank_account_holder_document"
          class="mt-2"
        />
      </div>

      <!-- Tipo de cuenta -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="customer-sex" class="mb-2" value="Tipo de cuenta" />

        <!-- Campo -->
        <select
          name="customer-sex"
          id="customer-sex"
          v-model="form.bank_account_type"
          class="
            w-full
            px-6
            py-2
            border border-gray-300
            focus:border-indigo-300
            focus:ring
            focus:ring-indigo-200
            focus:ring-opacity-50
            rounded-md
            shadow-sm
            text-sm text-gray-800
          "
        >
          <option :value="null">Selecciona el tipo</option>
          <option value="savings">Ahorros</option>
          <option value="current">Corriente</option>
        </select>

        <!-- Mensaje de error -->
        <jet-input-error
          :message="form.errors.bank_account_type"
          class="mt-2"
        />
      </div>
    </template>

    <template #actions>
      <jet-action-message :on="form.recentlySuccessful" class="mr-3">
        Guardado.
      </jet-action-message>

      <jet-button
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        Guardar
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import JetFormSection from "@/Jetstream/FormSection.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    JetFormSection,
    CustomLabel,
    JetInput,
    JetInputError,
    JetActionMessage,
    JetButton,
    JetSectionBorder,
    JetCheckbox,
  },
  props: ["customer"],
  setup(props) {
    const form = useForm({
      id: props.customer.information?.id,
      cashbox_id: props.customer.id,
      bank_name: props.customer.information?.bank_name,
      bank_account_number: props.customer.information?.bank_account_number,
      bank_account_type: props.customer.information?.bank_account_type
        ? props.customer.information?.bank_account_type
        : null,
      bank_account_holder: props.customer.information?.bank_account_holder,
      bank_account_holder_document:
        props.customer.information?.bank_account_holder_document,
    });
    return { form };
  },
  data() {
    return {
      customers: [],
    };
  },
  methods: {
    submit() {
      let url = route('customer.updateBankInformation', this.customer.id);
      this.form.put(url,{
        preserveScroll: true,
        preserveState: true,
        onError: errors => {
          console.log(errors);
        }
      })
    },
  },
};
</script>