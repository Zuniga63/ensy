<template>
  <jet-form-section @submitted="submit">
    <template #title>Información Comercial</template>
    <template #description>
      En este formulario se guarda la información del representante legal, nit e
      información bancaria.
    </template>

    <template #form>
      <!-- Representante legal -->
      <group-section title="Representante Legal">
        <div class="grid grid-cols-2 gap-4">
          <!-- Nombre del representante Legal-->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-legal-representative-first-name"
              class="mb-2"
              value="Nombres"
            />
            <!-- Input -->
            <jet-input
              id="business-legal-representative-first-name"
              name="business-legal-representative-first-name"
              type="text"
              v-model="form.legal_representative_first_name"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.legal_representative_first_name"
              class="mt-2"
            />
          </div>

          <!-- APellidos del representante Legal-->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-legal-representative-last-name"
              class="mb-2"
              value="Apellidos"
            />
            <!-- Input -->
            <jet-input
              id="business-legal-representative-last-name"
              name="business-legal-representative-last-name"
              type="text"
              v-model="form.legal_representative_last_name"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.legal_representative_last_name"
              class="mt-2"
            />
          </div>

          <!-- Documento -->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-legal-representative-document"
              class="mb-2"
              value="Numero de documento"
            />
            <!-- Input -->
            <jet-input
              id="business-legal-representative-document"
              name="business-legal-representative-document"
              type="text"
              v-model="form.legal_representative_document"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.legal_representative_document"
              class="mt-2"
            />
          </div>

          <!-- Tipo de identificación y sexo -->
          <div class="col-span-2 lg:col-span-1">
            <div class="grid grid-cols-2 gap-2">
              <!-- Tipo de identificación -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="legal-representative-document-type"
                  class="mb-2"
                  value="Tipo de Documento"
                />

                <!-- Campo -->
                <select
                  name="legal-representative-document-type"
                  id="legal-representative-document-type"
                  v-model="form.legal_representative_document_type"
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
                  <option value="CC">CC</option>
                  <option value="CE">CE</option>
                  <option value="PAP">PAP</option>
                </select>

                <jet-input-error
                  :message="form.errors.document_type"
                  class="mt-2"
                />
              </div>

              <!-- Sexo del cliente -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label for="business-legal-representative-sex" class="mb-2" value="Sexo" />

                <!-- Campo -->
                <select
                  name="business-legal-representative-sex"
                  id="business-legal-representative-sex"
                  v-model="form.legal_representative_sex"
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
                <jet-input-error :message="form.errors.legal_representative_sex" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Numero de telefono -->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label for="business-legal-tel" class="mb-2" value="Telefono" />
            <!-- Input -->
            <jet-input
              id="business-legal-tel"
              name="business-legal-tel"
              type="tel"
              v-model="form.legal_representative_tel"
              class="w-full mb-2"
              placeholder="Escribe el numero aquí."
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.legal_representative_tel" class="mt-2" />
          </div>

          <!-- Email -->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label for="business-legal-representative-email" class="mb-2" value="Email" />
            <!-- Input -->
            <jet-input
              id="business-legal-representative-email"
              name="business-legal-representative-email"
              type="text"
              v-model="form.legal_representative_email"
              class="w-full mb-2"
              placeholder="contacto@ejemplo.com"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.legal_representative_email" class="mt-2" />
          </div>
        </div>
      </group-section>

      <!-- Información Tributaria -->
      <group-section title="Información Tributaria">
        <div class="grid grid-cols-2 gap-4">
          <!-- NIT-->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-nit"
              class="mb-2"
              value="Nit"
            />
            <!-- Input -->
            <jet-input
              id="business-nit"
              name="business-legal-representative-first-name"
              type="text"
              v-model="form.nit"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.nit"
              class="mt-2"
            />
          </div>

          <!-- Nombre Tributario-->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-nit-name"
              class="mb-2"
              value="Nombre"
            />
            <!-- Input -->
            <jet-input
              id="business-nit-name"
              name="business-nit-name"
              type="text"
              v-model="form.nit_name"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.nit_name"
              class="mt-2"
            />
          </div>

          <!-- Fecha de renocación -->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-nit-renovation"
              class="mb-2"
              value="Fecha de ultima renovación"
            />
            <!-- Input -->
            <jet-input
              id="business-nit-renovation"
              name="business-nit-renovation"
              type="date"
              v-model="form.nit_date_of_renovation"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.nit_date_of_renovation"
              class="mt-2"
            />
          </div>
        </div>
      </group-section>

      <!-- Información Bancaria -->
      <group-section title="Información Bancaria">
        <div class="grid grid-cols-2 gap-4">
          <!-- Nombre del banco -->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-bank-name"
              class="mb-2"
              value="Banco"
            />
            <!-- Input -->
            <jet-input
              id="business-bank-name"
              name="business-bank-name"
              type="text"
              v-model="form.bank_name"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.bank_name"
              class="mt-2"
            />
          </div>

          <!-- Numero y tipo de cuenta -->
          <div class="col-span-2 lg:col-span-1">
            <div class="grid grid-cols-3 gap-2">
              <!-- Numero de cuenta -->
              <div class="col-span-2">
                <!-- Etiqueta -->
                <custom-label
                  for="business-bank-account"
                  class="mb-2"
                  value="Numero de cuenta"
                />
                <!-- Input -->
                <jet-input
                  id="business-bank-account"
                  name="business-bank-account"
                  type="text"
                  v-model="form.bank_account_number"
                  class="w-full mb-2"
                  placeholder="Escribelo aquí."
                />

                <!-- Error -->
                <jet-input-error
                  :message="form.errors.bank_account_number"
                  class="mt-2"
                />
              </div>

              <!-- Tipo de cuenta -->
              <div class="col-span-1">
                <!-- Etiqueta -->
                <custom-label for="business-bank-account-type" class="mb-2" value="Tipo" />

                <!-- Campo -->
                <select
                  name="business-bank-account-type"
                  id="business-bank-account-type"
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
                  <option value="savings">Ahorros</option>
                  <option value="current">Corriente</option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error :message="form.errors.bank_account_type" class="mt-2" />
              </div>
            </div>
          </div>

          <!-- Titular-->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-bank-account-holder"
              class="mb-2"
              value="Titular de la Cuenta"
            />
            <!-- Input -->
            <jet-input
              id="business-bank-account-holder"
              name="business-bank-account-holder"
              type="text"
              v-model="form.bank_account_holder"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.bank_account_holder"
              class="mt-2"
            />
          </div>

          <!-- Documento del titular -->
          <div class="col-span-2 lg:col-span-1">
            <!-- Etiqueta -->
            <custom-label
              for="business-bank-account-holder-document"
              class="mb-2"
              value="Documento del titular"
            />
            <!-- Input -->
            <jet-input
              id="business-bank-account-holder-document"
              name="business-bank-account-holder-document"
              type="text"
              v-model="form.bank_account_holder_document"
              class="w-full mb-2"
              placeholder="Escribelo aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.bank_account_holder_document"
              class="mt-2"
            />
          </div>
        </div>
      </group-section>
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
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import GroupSection from "@/Components/Form/InputGroupSection.vue";

export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    CustomLabel,
    JetCheckbox,
    GroupSection,
  },
  props: ['config'],
  setup(props) {
    const form = useForm({
      legal_representative_first_name: props.config.legal_representative_first_name,
      legal_representative_last_name: props.config.legal_representative_last_name,
      legal_representative_document: props.config.legal_representative_document,
      legal_representative_document_type: props.config.legal_representative_document_type,
      legal_representative_sex: props.config.legal_representative_sex,
      legal_representative_tel: props.config.legal_representative_tel,
      legal_representative_email: props.config.legal_representative_email,
      nit: props.config.nit,
      nit_name: props.config.nit_name,
      nit_date_of_renovation: props.config.nit_date_of_renovation,
      bank_name: props.config.bank_name,
      bank_account_type: props.config.bank_account_type,
      bank_account_number: props.config.bank_account_number,
      bank_account_holder: props.config.bank_account_holder,
      bank_account_holder_document: props.config.bank_account_holder_document,
    });

    return { form };
  },
  methods: {
    submit() {
      this.form.put(route("config.updateCommercialInformation"), {
        preserveScroll: true,
        onError: (errors) => {
          console.log(errors);
        },
      });
    },
  },
};
</script>