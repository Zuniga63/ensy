<template>
  <app-layout title="Cajas">
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <jet-form-section @submitted="createCashbox">
        <template #title>Registrar Caja</template>

        <template #form>
          <!-- Name -->
          <div class="col-span-6 sm:col-span-4">
            <jet-label for="cashboxName" value="Nombre" />
            <jet-input
              id="cashboxName"
              type="text"
              class="mt-1 block w-full"
              v-model.trim="form.name"
              autocomplete="cashboxName"
            />
            <jet-input-error :message="form.errors.name" class="mt-2" />
          </div>
          <!-- Code -->
          <div class="col-span-6 sm:col-span-4">
            <jet-label for="cashboxCode" value="Codigo" />
            <jet-input
              id="cashboxCode"
              type="text"
              class="mt-1 block w-full"
              v-model.trim="form.code"
              autocomplete="cashboxCode"
            />
            <jet-input-error :message="form.errors.code" class="mt-2" />
          </div>
        </template>

        <template #actions>
          <jet-action-message :on="form.recentlySuccessful" class="mr-3">
            Guardado.
          </jet-action-message>

          <Link :href="route('cashbox.index')" class="mr-3 underline text-sm text-gray-600 hover:text-gray-900">Cancelar</Link>

          <jet-button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Guardar
          </jet-button>
        </template>
      </jet-form-section>
    </div>
  </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import { useForm, Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetActionMessage,
    Link,
  },
  props: ["boxs"],
  setup(props) {
    const form = useForm({
      name: null,
      slug: null,
      code: null,
    });

    return { form };
  },
  methods: {
    createCashbox() {
      if (this.form.name) {
        this.form.slug = this.createSlug(this.form.name);
      }

      this.form.post(route("cashbox.store"));
    },
    validateForm() {
      //TODO
    },
    createSlug(name) {
      let result = "";
      if (typeof name === "string") {
        result = name
          .toLowerCase()
          .replace(/\s/gi, "_")
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "");
      }

      return result;
    },
  },
  mounted() {},
  computed: {},
};
</script>