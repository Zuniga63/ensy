<template>
  <jet-form-section @submitted="updateBasicConfig">
    <template #title> Información Basica </template>

    <template #description>
      En esta sección se guarda la información relacionada con el nombre del
      negocio los logos y los iconos del mismo.
    </template>

    <template #form>
      <!-- Logo Input Container -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Logo -->
        <input
          type="file"
          class="hidden"
          ref="logo"
          @change="updateLogoPreview"
        />

        <jet-label for="logo" value="Logo" />

        <!-- Current Logo Image -->
        <div class="mt-2" v-show="!logoPreview && logo">
          <img
            :src="logo"
            alt="logo"
            class="rounded-full h-20 w-20 object-cover"
          />
        </div>

        <!-- New Logo Preview -->
        <div class="mt-2" v-show="logoPreview">
          <span
            class="block w-20 h-20"
            :style="
              'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
              logoPreview +
              '\');'
            "
          >
          </span>
        </div>

        <div v-show="!logo && !logoPreview">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10 text-gray-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
        </div>

        <!-- Seleccionar Logo -->
        <jet-secondary-button
          class="mt-2 mr-2"
          type="button"
          @click.prevent="selectNewLogo"
        >
          Seleccionar Logo
        </jet-secondary-button>

        <jet-secondary-button
          type="button"
          class="mt-2"
          @click.prevent="deleteLogo"
          v-if="logo"
        >
          Eliminar
        </jet-secondary-button>

        <jet-input-error :message="form.errors.logo" class="mt-2" />
      </div>

      <!-- Favicon input container -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Favicon -->
        <div>
          <input
            type="file"
            class="hidden"
            ref="favicon"
            @change="updateFaviconPreview"
          />

          <jet-label for="logo" value="Favicon" />

          <!-- Current Favicon Image -->
          <div class="mt-2" v-show="!faviconPreview && favicon">
            <img
              :src="favicon"
              alt="favicon"
              class="rounded-full h-20 w-20 object-cover"
            />
          </div>

          <!-- New Favicon Preview -->
          <div class="mt-2" v-show="faviconPreview">
            <span
              class="block w-20 h-20"
              :style="
                'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
                faviconPreview +
                '\');'
              "
            >
            </span>
          </div>

          <div v-show="!favicon && !faviconPreview">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-10 w-10 text-gray-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>

          <!-- Seleccionar Favicon -->
          <jet-secondary-button
            class="mt-2 mr-2"
            type="button"
            @click.prevent="selectNewFavicon"
          >
            Seleccionar Favicon
          </jet-secondary-button>

          <jet-secondary-button
            type="button"
            class="mt-2"
            @click.prevent="deleteFavicon"
            v-if="favicon"
          >
            Eliminar
          </jet-secondary-button>

          <jet-input-error :message="form.errors.favicon" class="mt-2" />
        </div>
      </div>

      <!-- Name -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="name" value="Nombre" />
        <jet-input
          id="name"
          type="text"
          class="mt-1 block w-full"
          v-model="form.name"
          autocomplete="name"
        />
        <jet-input-error :message="form.errors.name" class="mt-2" />
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
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
  },

  data() {
    return {
      logoPreview: null,
      faviconPreview: null,
      form: useForm({
        _method: "PUT",
        name: this.$page.props.businessConfig.name,
        logo: null,
        favicon: null,
      }),
    };
  },
  methods: {
    /**
     * Se encarga de ejecutar la ventana que
     * permite seleccionar la imagen
     */
    selectNewLogo() {
      this.$refs.logo.click();
    },

    /**
     * Carga en el navegador la imagen
     * que se usa como favicon
     */
    selectNewFavicon() {
      this.$refs.favicon.click();
    },

    /**
     * Actualiza la imagen del logo con el archivo
     * que ha sido cargado en el formulario
     */
    updateLogoPreview() {
      const newLogo = this.$refs.logo.files[0];
      if (!newLogo) return;

      const reader = new FileReader();

      reader.onload = (e) => {
        this.logoPreview = e.target.result;
      };

      reader.readAsDataURL(newLogo);
    },

    /**
     * Actualiza la url del archivo cargado
     * para ser usado como favicon
     */
    updateFaviconPreview() {
      const newFavicon = this.$refs.favicon.files[0];
      if (!newFavicon) return;

      const reader = new FileReader();

      reader.onload = (e) => {
        this.faviconPreview = e.target.result;
      };

      reader.readAsDataURL(newFavicon);
    },

    updateBasicConfig() {
      //Se agrega el archivo con la imagen del logo
      if (this.$refs.logo) {
        this.form.logo = this.$refs.logo.files[0];
      }

      if (this.$refs.favicon) {
        this.form.favicon = this.$refs.favicon.files[0];
      }

      this.form.post(route("config.updateBasicConfig"), {
        preserveScroll: true,
        onSuccess: () => {
          this.logoPreview = null;
          this.faviconPreview = null;
          this.clearFileInputs();
        },
      });
    },
    clearFileInputs() {
      if (this.$refs.logo?.value) {
        this.$refs.logo.value = null;
      }

      if (this.$refs.favicon?.value) {
        this.$refs.favicon.value = null;
      }
    },
    deleteLogo() {
      this.$inertia.delete(route("config.deleteLogo"), {
        preserveScroll: true,
        onSuccess: () => {
          this.logoPreview = null;
          this.clearFileInputs();
        },
      });
    },
    deleteFavicon() {
      this.$inertia.delete(route("config.deleteFavicon"), {
        preserveScroll: true,
        onSuccess: () => {
          this.faviconPreview = null;
          this.clearFileInputs();
        },
      });
    },
  },
  computed: {
    logo() {
      return this.$page.props.businessConfig.logo;
    },
    favicon() {
      return this.$page.props.businessConfig.favicon;
    },
  },
};
</script>