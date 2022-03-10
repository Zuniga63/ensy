<template>
  <jet-form-section @submitted="submit">
    <template #title> Información Basica </template>

    <template #description>
      Información basica para la identificación de clientes. <br />
      <template v-if="customers.length > 0">
        Agregados:
        <span v-for="(name, index) in customers" :key="index">
          <span class="font-bold"> {{ name }} </span>
          <span v-if="index < customers.length - 1">&#44;&#32;</span>
        </span>
      </template>
    </template>

    <template #form>
      <!-- Nombres del cliente -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-first-name"
          required
          class="mb-2"
          value="Nombres"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-first-name"
          class="w-full"
          placeholder="Escribe los nombres del cliente"
          v-model="form.first_name"
        />
        <jet-input-error :message="form.errors.first_name" class="mt-2" />
      </div>

      <!-- Apellidos del cliente -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="customer-last-name" class="mb-2" value="Apellidos" />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-last-name"
          class="w-full"
          placeholder="Escribe los nombres del cliente"
          v-model="form.last_name"
        />

        <!-- Mensaje de error -->
        <jet-input-error :message="form.errors.last_name" class="mt-2" />
      </div>

      <!-- Numero de identificación -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-document-number"
          class="mb-2"
          value="Numero de Identificación"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-document-number"
          class="w-full tracking-widest"
          placeholder="Escribe el numero de identificación"
          v-model="form.document_number"
        />

        <!-- Error -->
        <jet-input-error :message="form.errors.document_number" class="mt-2" />
      </div>

      <!-- Tipo de identificación -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-document-type"
          class="mb-2"
          value="Tipo de Documento"
        />

        <!-- Campo -->
        <select
          name="customer-document-type"
          id="customer-document-type"
          v-model="form.document_type"
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
          <option value="CC">Cedula de Ciudadanía</option>
          <option value="CE">Cedula de Extranjería</option>
          <option value="TI">Tarjeta de Identidad</option>
          <option value="NIT">NIT</option>
          <option value="NIP">Numero de Identificacion Personal NIP</option>
          <option value="PAP">Pasaporte</option>
        </select>

        <jet-input-error :message="form.errors.document_type" class="mt-2" />
      </div>

      <!-- Sexo del cliente -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="customer-sex" class="mb-2" value="Sexo" />

        <!-- Campo -->
        <select
          name="customer-sex"
          id="customer-sex"
          v-model="form.sex"
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
          <option :value="null">Selecciona el sexo</option>
          <option value="f">Mujer</option>
          <option value="m">Hombre</option>
        </select>

        <!-- Mensaje de error -->
        <jet-input-error :message="form.errors.sex" class="mt-2" />
      </div>

      <!-- Correo del cliente -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-email"
          class="mb-2"
          value="Correo Electronico"
        />

        <!-- Campo -->
        <jet-input
          type="email"
          id="customer-email"
          class="w-full"
          placeholder="ejemplo@ejemplo.com"
          v-model="form.email"
        />

        <!-- Campo de error -->
        <jet-input-error :message="form.errors.email" class="mt-2" />
      </div>
    </template>

    <template #actions>
      <jet-action-message :on="form.recentlySuccessful" class="mr-3">
        Guardado.
      </jet-action-message>

      <label class="flex items-center mr-3" v-if="!customer">
        <jet-checkbox name="remember" v-model:checked="form.addOtherCustomer" />
        <span class="ml-2 text-sm text-gray-600">Agregar Otro</span>
      </label>

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
import { onBeforeUpdate } from "@vue/runtime-core";

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
      id: null,
      first_name: null,
      last_name: null,
      email: null,
      sex: null,
      document_number: null,
      document_type: "CC",
      addOtherCustomer: false,
    });
    return { form };
  },
  data() {
    return {
      customers: [],
    };
  },
  beforeMount() {
    this.updatForm();
  },
  methods: {
    submit() {
      let url = null;
      if (this.customer) {
        url = route("customer.update", this.customer.id);
        this.form.put(url, {
          preserveScroll: true,
          preserveState: true,
          onSuccess: (page) => {
            this.updatForm();
          },
        });
      } else {
        let url = route("customer.store");
        this.form.post(url, {
          preserveState: true,
          onSuccess: (page) => {
            let res = page.props.flash.message;

            if (res.ok && res.reload) {
              let firstName = res.customer.first_name;
              let lastName = res.customer.last_name;
              let fullName = `${firstName} ${lastName}`.trim();
              this.customers.push(fullName);

              this.form.reset();
              this.form.addOtherCustomer = true;
            }
          },
        });
      }
    },
    updatForm() {
      if (this.customer) {
        this.form.id = this.customer.id;
        this.form.first_name = this.customer.first_name;
        this.form.last_name = this.customer.last_name;
        this.form.email = this.customer.email;
        this.form.sex = this.customer.sex;
        this.form.document_number = this.customer.document_number;
        this.form.document_type = this.customer.document_type;
      }
    },
  },
};
</script>