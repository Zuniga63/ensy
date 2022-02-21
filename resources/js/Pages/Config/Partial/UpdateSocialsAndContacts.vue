<template>
  <jet-form-section @submitted="submit">
    <template #title>Información de Contacto y Redes</template>
    <template #description>
      Este formulario permite guardar la información de contacto como número de
      teléfono, correo y los enlaces a las redes sociales como Facebook,
      Instagram y WhatsApp.
    </template>

    <template #form>
      <!-- Numero de telefono -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="business-phone" class="mb-2" value="Telefono" />
        <!-- Input -->
        <jet-input
          id="business-phone"
          name="business-phone"
          type="tel"
          v-model="form.phone"
          class="w-full mb-2"
          placeholder="Escribe el numero aquí."
        />
        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.show_phone"
            id="business-show-phone"
          />
          <label for="business-show-phone" class="text-sm text-gray-800"
            >Mostrar Telefono</label
          >
        </div>

        <!-- Error -->
        <jet-input-error :message="form.errors.phone" class="mt-2" />
      </div>

      <!-- Numero de WhatsApp -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="business-whatsapp" class="mb-2" value="WhatsApp" />
        <!-- Input -->
        <jet-input
          id="business-whatsapp"
          name="business-whatsapp"
          type="tel"
          v-model="form.whatsapp"
          class="w-full mb-2"
          placeholder="Escribe el numero aquí."
        />
        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.show_whatsapp"
            id="business-show-whatsapp"
          />
          <label for="business-show-whatsapp" class="text-sm text-gray-800"
            >Mostrar WhatsApp</label
          >
        </div>

        <!-- Error -->
        <jet-input-error :message="form.errors.whatsapp" class="mt-2" />
      </div>

      <!-- Dirección -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="business-address" class="mb-2" value="Dirección" />
        <!-- Input -->
        <jet-input
          id="business-address"
          name="business-address"
          type="text"
          v-model="form.address"
          class="w-full mb-2"
          placeholder="Escribe la dirección aquí."
        />
        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.show_address"
            id="business-show-address"
          />
          <label for="business-show-address" class="text-sm text-gray-800"
            >Mostrar Dirección</label
          >
        </div>

        <!-- Error -->
        <jet-input-error :message="form.errors.address" class="mt-2" />
      </div>

      <!-- Email -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="business-email" class="mb-2" value="Email" />
        <!-- Input -->
        <jet-input
          id="business-email"
          name="business-email"
          type="text"
          v-model="form.email"
          class="w-full mb-2"
          placeholder="contacto@ejemplo.com"
        />
        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.show_email"
            id="business-show-email"
          />
          <label for="business-show-email" class="text-sm text-gray-800"
            >Mostrar Email</label
          >
        </div>

        <!-- Error -->
        <jet-input-error :message="form.errors.email" class="mt-2" />
      </div>

      <!-- Facebook -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="business-facebook-nick"
          class="mb-2"
          value="Facebook Nick"
        />
        <!-- Input -->
        <jet-input
          id="business-facebook-nick"
          name="business-facebook-nick"
          type="text"
          v-model="form.facebook_nick"
          class="w-full mb-2"
          placeholder="@facebook_nick"
        />
        <!-- Error -->
        <jet-input-error :message="form.errors.facebook_nick" class="mb-2" />

        <custom-label
          for="business-facebook-link"
          class="mb-2"
          value="Facebook Link"
        />
        <!-- Input -->
        <jet-input
          id="business-facebook-link"
          name="business-facebook-link"
          type="text"
          v-model="form.facebook_link"
          class="w-full mb-2"
          placeholder="https://facebook.com/nick"
        />
        <!-- Error -->
        <jet-input-error :message="form.errors.facebook_link" class="mb-2" />

        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.show_facebook"
            id="business-show-facebook"
          />
          <label for="business-show-facebook" class="text-sm text-gray-800"
            >Mostrar Facebook</label
          >
        </div>
      </div>

      <!-- Instagram -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="business-instagram-nick"
          class="mb-2"
          value="Instagram Nick"
        />
        <!-- Input -->
        <jet-input
          id="business-instagram-nick"
          name="business-instagram-nick"
          type="text"
          v-model="form.instagram_nick"
          class="w-full mb-2"
          placeholder="@instagram_nick"
        />
        <!-- Error -->
        <jet-input-error :message="form.errors.instagram_nick" class="mb-2" />

        <custom-label
          for="business-instagram-link"
          class="mb-2"
          value="Instagram Link"
        />
        <!-- Input -->
        <jet-input
          id="business-instagram-link"
          name="business-instagram-link"
          type="text"
          v-model="form.instagram_link"
          class="w-full mb-2"
          placeholder="https://instagram.com/nick"
        />
        <!-- Error -->
        <jet-input-error :message="form.errors.instagram_link" class="mb-2" />

        <!-- Checked -->
        <div class="flex items-center">
          <JetCheckbox
            class="mx-2"
            v-model:checked="form.show_instagram"
            id="business-show-instagram"
          />
          <label for="business-show-instagram" class="text-sm text-gray-800"
            >Mostrar Instagram</label
          >
        </div>
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
import CustomLabel from "@/Components/Form/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    CustomLabel,
    JetCheckbox,
  },
  setup(props) {
    const form = useForm({
      phone: null,
      show_phone: null,
      address: null,
      show_address: null,
      email: null,
      show_email: null,
      whatsapp: null,
      show_whatsapp: null,
      facebook_nick: null,
      facebook_link: null,
      show_facebook: null,
      instagram_nick: null,
      instagram_link: null,
      show_instagram: null,
    });

    return {form};
  },
  methods: {
    updateForm() {
      let businessConfig = this.$page.props.businessConfig;

      this.form.phone = businessConfig.phone.number;
      this.form.show_phone = businessConfig.phone.show;
      this.form.address = businessConfig.address.value;
      this.form.show_address = businessConfig.address.show;
      this.form.email = businessConfig.email.value;
      this.form.show_email = businessConfig.email.show;
      this.form.whatsapp = businessConfig.whatsapp.number;
      this.form.show_whatsapp = businessConfig.whatsapp.show;
      this.form.facebook_nick = businessConfig.facebook.nick;
      this.form.facebook_link = businessConfig.facebook.link;
      this.form.show_facebook = businessConfig.facebook.show;
      this.form.instagram_nick = businessConfig.instagram.nick;
      this.form.instagram_link = businessConfig.instagram.link;
      this.form.show_instagram = businessConfig.instagram.show;
    },
    submit() {
      this.form.put(route('config.updateSocialsAndContacts'),{
        preserveScroll:true,
        onSuccess: (page)=>{
          this.updateForm();
        },
        onError: (errors) => {
          console.log(errors);
        }

      })
    },
  },
  beforeMount() {
    this.updateForm();
  },
};
</script>