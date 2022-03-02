<template>
  <jet-form-section @submitted="submit">
    <template #title> Numeros de contacto </template>

    <template #description>
      Guarda los numeros de contacto del cliente.
    </template>

    <template #form>
      <!-- Descripción del telefono -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-contact-description"
          class="mb-2"
          value="Descripción"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="customer-contac-description"
          class="w-full"
          placeholder="Escribe una descripción"
          v-model="form.description"
        />
        <jet-input-error :message="form.errors.bank_name" class="mt-2" />
      </div>
      <!-- Numero -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="customer-contact-number"
          class="mb-2"
          value="Numero"
          required
        />
        <!-- Input -->
        <jet-input
          id="customer-contact-number"
          name="customer-contact-number"
          type="tel"
          v-model="form.number"
          class="w-full mb-2"
          placeholder="Escribe el numero aquí."
        />
        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.whatsapp"
            id="customer-contact-whatsapp"
          />
          <label for="customer-contact-whatsapp" class="text-sm text-gray-800"
            >Numero de whatsapp</label
          >
        </div>

        <!-- Error -->
        <jet-input-error :message="form.errors.whatsapp" class="mt-2" />
      </div>

      <!-- Numeros actuales -->
      <div class="col-span-6">
        <table class="w-full table-auto">
          <thead class="bg-gray-200 text-gray-800">
            <tr>
              <th class="text-center">#</th>
              <th>Descripción</th>
              <th class="text-center">Numero</th>
              <th class="text-center">WhatsApp</th>
              <th scope="col" class="relative">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(contact, index) in customer.contacts"
              :key="contact.id"
              class="text-gray-800"
            >
              <!-- Index -->
              <td class="text-center">{{ index + 1 }}</td>
              <!-- Descripción -->
              <td>{{ contact.description }}</td>
              <!-- Numero -->
              <td class="text-center">{{ contact.number }}</td>
              <!-- WhatsApp -->
              <td class="text-center">{{ contact.whatsapp ? "Si" : "No" }}</td>
              <td>
                <a href="javascript:;" class="text-red-500" @click="deletePhone(contact.id)">
                  <trash-icon class="h-4 w-4" />
                </a>
              </td>
            </tr>
          </tbody>
        </table>
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
import TrashIcon from "@/Components/Svgs/Trash.vue";

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
    TrashIcon,
  },
  props: ["customer"],
  setup(props) {
    const form = useForm({
      number: null,
      description: null,
      whatsapp: false,
    });
    return { form };
  },
  data() {
    return {
      phones: [],
    };
  },
  methods: {
    submit() {
      let url = route("customer.sotreContact", this.customer.id);
      this.form.post(url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          this.form.reset();
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    },
    deletePhone(contactId) {
      this.$inertia.delete(
        route("customer.destroyContact", [this.customer.id, contactId]),
        {
          preserveScroll: true,
          preserveState: true,
          onError: (errors) => {
            console.log(errors);
          },
        }
      );
    },
  },
};
</script>