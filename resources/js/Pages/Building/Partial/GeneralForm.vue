<template>
  <jet-form-section @submitted="submit">
    <template #title> Información General </template>

    <template #description>
      Formulario para actualizar la imagen, descripción y otras caracteristicas.
    </template>

    <template #form>
      <!-- Imagen, descripción y disponibilidad-->
      <div class="col-span-6 lg:col-span-3">
        <div class="flex flex-col items-center">
          <!-- Image -->
          <div
            class="
              relative
              w-60
              h-60
              mb-2
              ring ring-gray-500
              rounded-sm
              overflow-hidden
              hover:cursor-pointer
            "
            @click="$refs.image.click()"
          >
            <!-- Cuando no hay imagen -->
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 scale-90"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-200"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-90"
            >
              <div
                class="
                  w-full
                  h-full
                  flex
                  items-center
                  justify-center
                  text-gray-600
                  bg-gray-100
                "
                v-show="!imagePreview && !imageUrl"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-40 w-40"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>
            </transition>

            <!-- Cuando el inmueble tiene imagen -->
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 scale-90"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-200"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-90"
            >
              <img
                v-show="!imagePreview && imageUrl"
                :src="imageUrl"
                alt="Imagen Actual"
                class="block w-full h-full object-cover object-center"
              />
            </transition>

            <!-- Cuando la imagen ha sido cargada -->
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 scale-90"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition ease-in duration-200"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-90"
            >
              <img
                v-show="imagePreview"
                :src="imagePreview"
                alt="Imagen del inmueble"
                class="block w-full h-full object-cover object-center"
              />
            </transition>

            <!-- Boton para Quitar -->
            <a
              href="javascript:;"
              class="
                absolute
                top-2
                right-2
                p-1
                rounded-full
                border border-red-200
                bg-white
                text-red-500
                hover:border-red-500 hover:bg-red-50
                transition-colors
                z-10
              "
              title="Remover Imagen"
              v-show="imagePreview || imageUrl"
              @click.stop="removeImagePreview"
            >
              <trash-icon />
            </a>
            <!-- Boton para deshacer -->
            <a
              href="javascript:;"
              class="
                absolute
                top-2
                right-2
                p-1
                rounded-full
                border border-indigo-200
                bg-indigo-100
                text-indigo-500
                hover:border-indigo-500 hover:bg-indigo-50
                transition-colors
                z-10
              "
              title="Deshacer Eliminación"
              v-show="building.image_url && !imageUrl"
              @click.stop="undoDelete"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"
                />
              </svg>
            </a>
          </div>

          <!-- Input -->
          <input
            type="file"
            class="hidden"
            ref="image"
            @change="updateImagePreview"
            accept="image/*"
          />

          <jet-input-error :message="form.errors.image" class="mt-2" />

          <!-- upload button -->
          <jet-secondary-button
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            type="button"
            @click="$refs.image.click()"
          >
            <div class="flex items-center">
              <!-- Heroicon:cloud-upload -->
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"
                />
                <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
              </svg>

              <span>Subir Imagen</span>
            </div>
          </jet-secondary-button>

          <!-- Description -->
          <div class="self-stretch mt-2">
            <custom-label
              for="building-description"
              value="Descripción"
              class="mb-2"
            />

            <textarea
              name="building-description"
              id="building-description"
              cols="30"
              rows="5"
              class="
                block
                w-full
                border border-gray-200
                rounded
                text-sm text-gray-800
                focus:ring focus:ring-indigo-400 focus:ring-opacity-40
              "
              placeholder="Escribe aquí una descripción del inmueble."
              v-model="form.description"
              ref="description"
            ></textarea>

            <!-- Error -->
            <jet-input-error :message="form.errors.description" class="mt-2" />
          </div>
        </div>
      </div>
      <!-- Información general -->
      <div class="col-span-6 lg:col-span-3">
        <div class="grid grid-cols-2 gap-2">
          <!-- Habitaciones -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-rooms"
              class="mb-2"
              value="Habitaciones"
            />
            <!-- Input -->
            <jet-input
              id="building-rooms"
              name="building-rooms"
              type="number"
              v-model="form.rooms"
              class="w-full text-center text-sm"
              placeholder="# Hab."
              min="1"
              max="100"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.rooms" class="mt-2" />
          </div>

          <!-- Baños -->
          <div>
            <!-- Etiqueta -->
            <custom-label for="building-bathrooms" class="mb-2" value="Baños" />
            <!-- Input -->
            <jet-input
              id="building-bathrooms"
              name="building-bathrooms"
              type="number"
              v-model="form.bathrooms"
              class="w-full text-center text-sm"
              placeholder="# Baños"
              min="1"
              max="100"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.bathrooms" class="mt-2" />
          </div>

          <!-- Paqueaderos -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-parking-lots"
              class="mb-2"
              value="Parqueaderos"
            />
            <!-- Input -->
            <jet-input
              id="building-parking-lots"
              name="building-parking-lots"
              type="number"
              v-model="form.parking_lots"
              class="w-full text-center text-sm"
              placeholder="# parking"
              min="1"
              max="100"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.parking_lots" class="mt-2" />
          </div>

          <!-- Estrato -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-socioeconomics"
              class="mb-2"
              value="Estrato"
            />
            <!-- Input -->
            <jet-input
              id="building-socioeconomics"
              name="building-socioeconomics"
              type="number"
              v-model="form.socioeconomic"
              class="w-full text-center text-sm"
              placeholder="# estrato"
              min="1"
              max="100"
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.socioeconomic"
              class="mt-2"
            />
          </div>

          <!-- Area total -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-area"
              class="mb-2"
              value="Área Total [m2]"
            />
            <!-- Input -->
            <jet-input
              id="building-area"
              name="building-area"
              type="number"
              v-model="form.area"
              class="w-full text-center text-sm"
              placeholder="área"
              min="1"
              step="0.1"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.area" class="mt-2" />
          </div>

          <!-- Area privada -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-private-area"
              class="mb-2"
              value="Area Privada [m2]"
            />
            <!-- Input -->
            <jet-input
              id="building-private-area"
              name="building-private-area"
              type="number"
              v-model="form.private_area"
              class="w-full text-center text-sm"
              placeholder="área"
              min="1"
              step="0.1"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.private_area" class="mt-2" />
          </div>

          <!-- Piso -->
          <div>
            <!-- Etiqueta -->
            <custom-label for="building-floor" class="mb-2" value="Piso" />
            <!-- Input -->
            <jet-input
              id="building-floor"
              name="building-floor"
              type="number"
              v-model="form.floor"
              class="w-full text-center text-sm"
              placeholder="# piso"
              min="1"
              max="100"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.floor" class="mt-2" />
          </div>

          <!-- Antiguedad -->
          <div>
            <!-- Etiqueta -->
            <custom-label for="building-antiquity" class="mb-2">
              Antiguedad
              <span class="text-xs text-gray-400" v-if="antiquity"
                >[{{ antiquity }}]</span
              >
            </custom-label>
            <!-- Input -->
            <jet-input
              id="building-antiquity"
              name="building-antiquity"
              type="date"
              v-model="form.antiquity"
              class="w-full text-center text-sm"
              placeholder="Fecha"
              :max="maxDate"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.antiquity" class="mt-2" />
          </div>

          <!-- Campo para otras caracteristicas -->
          <div class="col-span-2">
            <custom-label
              for="building-pfeatures"
              class="mb-2"
              value="Mas Caracteristicas"
            />

            <div class="grid grid-cols-6 gap-1 items-center mb-2">
              <jet-input
                id="building-features"
                name="building-pfeatures"
                type="text"
                v-model.trim="feature"
                class="col-span-5 w-full text-center"
                placeholder="Escribela aquí."
                @keydown.prevent.enter="addFeature"
              />

              <div class="flex items-center justify-center">
                <a
                  href="javascript:;"
                  class="
                    p-2
                    border border-gray-200
                    rounded-full
                    text-gray-600
                    hover:bg-green-400 hover:border-green-500 hover:text-gray-50
                    transition-colors
                    shadow
                  "
                  @click="addFeature"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 4v16m8-8H4"
                    />
                  </svg>
                </a>
              </div>
            </div>

            <!-- Listado de caracteristicas -->
            <div
              class="
                flex flex-wrap
                items-start
                border
                p-2
                border-gray-200
                rounded-sm
                h-20
                overflow-y-auto
              "
            >
              <div
                v-for="(item, index) in features"
                :key="index"
                class="
                  px-2
                  py-1
                  mr-2
                  mb-1
                  border border-gray-200
                  rounded
                  text-xs text-gray-600
                  hover:cursor-pointer
                  hover:bg-red-50
                  hover:border-red-400
                  hover:text-red-500
                  transition-colors
                  select-none
                "
                @click="removeFeature(index)"
              >
                {{ item }}
              </div>
            </div>
          </div>
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
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import TrashIcon from "@/Components/Svgs/Trash.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";
import localizedFormat from "dayjs/plugin/localizedFormat";

export default {
  components: {
    JetFormSection,
    CustomLabel,
    JetInput,
    JetInputError,
    JetActionMessage,
    JetButton,
    JetCheckbox,
    JetSecondaryButton,
    TrashIcon,
  },
  props: ["building"],
  setup(props) {
    const form = useForm({
      _method: "PUT",
      image: null,
      deleteImage: false,
      description: props.building.description,
      features: null,
      socioeconomic: props.building.socioeconomic,
      rooms: props.building.rooms,
      bathrooms: props.building.bathrooms,
      parking_lots: props.building.parking_lots,
      area: props.building.area,
      private_area: props.building.private_area,
      floor: props.building.floor,
      antiquity: props.building.antiquity,
      available: props.building.available,
    });

    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);
    dayjs.extend(localizedFormat);

    return { form };
  },
  data() {
    return {
      imagePreview: null,
      imageUrl: this.building.image_url,
      feature: null,
      features: [],
    };
  },
  beforeMount() {
    if (this.building.features) this.features = this.building.features;
  },
  methods: {
    submit() {
      let url = route("building.update", this.building.id);
      if (this.features.length > 0) this.form.features = this.features;

      this.form.post(url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          //console.log(page.props.flash.message);
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    },
    /**
     * Se encarga de montar la imagen al formulario
     * y de actualizar la previsualización del archivo en pantalla.
     */
    updateImagePreview() {
      const newImage = this.$refs.image.files[0];
      if (!newImage) return;

      this.form.image = newImage;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.imagePreview = e.target.result;
      };

      reader.readAsDataURL(newImage);
    },
    /**
     * Retira el archivo tanto del campo como del formulario
     * y resetea la propiedad de la imagen.
     */
    removeImagePreview() {
      if (this.imagePreview && this.$refs.image) {
        this.$refs.image.value = null;
        this.imagePreview = null;
        this.form.image = null;
      } else if (this.imageUrl) {
        this.imageUrl = null;
        this.form.deleteImage = true;
      }
    },
    /**
     * Vuelve a cargar la imagen original del inmueble
     * y establece en false la propiedad para elimnar la imagen.
     */
    undoDelete() {
      this.imageUrl = this.building.image_url;
      this.form.deleteImage = false;
    },
    /**
     * Agrega la caracteristica escrita en el campo
     * al listado.
     */
    addFeature() {
      if (this.feature && typeof this.feature === "string") {
        let text = this.feature.toLowerCase();
        text = text.charAt(0).toUpperCase() + text.slice(1);
        this.features.push(text);
        this.feature = null;
      }
    },
    /**
     * Retira la caracteristica del listado de caracteristicas
     * @param {Number} index
     */
    removeFeature(index) {
      this.features.splice(index, 1);
    },
  },
  computed: {
    maxDate() {
      return dayjs().format("YYYY-MM-DD");
    },
    antiquity() {
      if (this.form.antiquity) {
        return dayjs(this.form.antiquity).fromNow(true);
      }

      return null;
    },
  },
};
</script>