<template>
  <app-layout title="Crear Administración">
    <template #header>
      <div class="flex justify-between">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Actualizar Administración
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('buildingAdmin.index')">
          Regresar
        </link-button>
      </div>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <jet-form-section @submitted="submit">
        <template #title> Formulario de Actualización </template>
        <template #description>
          Se va a actualizar la información del grupo de administracion <span class="font-bold">{{buildingAdmin.name}}</span>.
        </template>

        <template #form>
          <!-- Nombre del grupo adnimistrativo -->
          <div class="col-span-6 lg:col-span-3">
            <!-- Etiqueta -->
            <custom-label
              for="admin-building-name"
              class="mb-2"
              value="Nombre del Edificio"
              required
            />
            <!-- Input -->
            <jet-input
              id="admin-building-name"
              name="admin-building-name"
              type="text"
              v-model="form.name"
              class="w-full"
              placeholder="Escribe el nombre del edificio aquí."
              ref="input"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.name" class="mt-2" />
          </div>

          <!-- Dirección de la administración -->
          <div class="col-span-6 lg:col-span-3">
            <!-- Etiqueta -->
            <custom-label
              for="admin-building-address"
              class="mb-2"
              value="Dirección"
            />
            <!-- Input -->
            <jet-input
              id="admin-building-address"
              name="admin-building-address"
              type="text"
              v-model="form.address"
              class="w-full"
              placeholder="Escribe la dirección aquí."
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.address" class="mt-2" />
          </div>

          <!-- Nombre del administrador -->
          <div class="col-span-6 lg:col-span-2">
            <!-- Etiqueta -->
            <custom-label
              for="adfirst_name"
              class="mb-2"
              value="Nombres del Administrador"
            />
            <!-- Input -->
            <jet-input
              id="adfirst_name"
              name="adfirst_name"
              type="text"
              v-model="form.admin_first_name"
              class="w-full"
              placeholder="Escribe el nombre aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.admin_first_name"
              class="mt-2"
            />
          </div>

          <!-- Apellidos del adminsitrador -->
          <div class="col-span-6 lg:col-span-2">
            <!-- Etiqueta -->
            <custom-label
              for="admin-last-name"
              class="mb-2"
              value="Apellidos del Administrador"
            />
            <!-- Input -->
            <jet-input
              id="admin-last-name"
              name="admin-last-name"
              type="text"
              v-model="form.admin_last_name"
              class="w-full"
              placeholder="Escribe los apellidos aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.admin_last_name"
              class="mt-2"
            />
          </div>

          <!-- Documento -->
          <div class="col-span-6 lg:col-span-2">
            <!-- Etiqueta -->
            <custom-label
              for="admin-document_number"
              class="mb-2"
              value="Documento de Identificación"
            />
            <!-- Input -->
            <jet-input
              id="admin-document_number"
              name="admin-document_number"
              type="text"
              v-model="form.admin_document_number"
              class="w-full"
              placeholder="Escribe el documento aquí."
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.admin_document_number"
              class="mt-2"
            />
          </div>

          <!-- Correo Electronico -->
          <div class="col-span-6 lg:col-span-3">
            <!-- Etiqueta -->
            <custom-label
              for="admin-email"
              class="mb-2"
              value="Correo Electronico"
            />
            <!-- Input -->
            <jet-input
              id="admin-email"
              name="admin-email"
              type="email"
              v-model="form.email"
              class="w-full"
              placeholder="ejemplo@ejemplo.com"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.email" class="mt-2" />
          </div>

          <!-- Telefono -->
          <div class="col-span-6 lg:col-span-3">
            <!-- Etiqueta -->
            <custom-label for="admin-phone" class="mb-2" value="Telefono" />
            <!-- Input -->
            <div class="flex mb-2 items-center">
              <!-- Numero de telefono -->
              <jet-input
                id="admin-phone"
                name="admin-phone"
                type="tel"
                v-model="phoneNumber"
                class="w-full mr-2"
                placeholder="555-555-5555"
              />
              <!-- Transición para extensiones -->
              <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-90"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-90"
              >
                <!-- Contendor de Extensiones -->
                <div class="flex mr-2" v-show="hasExtension">
                  <!-- Extensión #1 -->
                  <jet-input
                    type="text"
                    class="w-20 mr-2"
                    placeholder="EXT-1"
                    v-model="ext1"
                  />
                  <!-- Extensión #2 -->
                  <jet-input
                    type="text"
                    class="w-20"
                    placeholder="EXT-2"
                    v-model="ext2"
                  />
                </div>
              </transition>

              <!-- Boton para agregar telefono al listado -->
              <button
                type="button"
                class="
                  py-2
                  px-3
                  border border-blue-200
                  rounded-md
                  text-sm text-white
                  font-bold
                  bg-blue-500
                  shadow
                  hover:bg-white
                  hover:text-gray-800
                  hover:ring
                  hover:ring-blue-500
                  hover:ring-opacity-20
                "
                @click="addPhone"
              >
                +
              </button>
            </div>

            <!-- Error -->
            <jet-input-error :message="form.errors.phones" class="mt-2" />
            <!-- Checked -->
            <div class="flex items-center">
              <JetCheckbox
                class="mx-2"
                v-model:checked="hasExtension"
                id="business-show-email"
              />
              <label for="business-show-email" class="text-sm text-gray-800"
                >Tiene Extensiones</label
              >
            </div>
          </div>

          <!-- Listado de telefonos -->
          <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
          >
            <div
              v-show="phones.length > 0"
              class="
                relative
                col-span-6
                border border-gray-200
                rounded
                px-4
                pt-6
                pb-4
              "
            >
              <!-- Pill -->
              <p
                class="
                  absolute
                  top-0
                  inline-block
                  px-4
                  text-sm text-gray-800
                  border border-gray-200
                  rounded-full
                  bg-white
                  transform
                  -translate-y-1/2
                "
              >
                Listado de Telefonos
              </p>
              <!-- Listado -->
              <ul class="flex flex-wrap">
                <li
                  v-for="(item, index) in phones"
                  :key="index"
                  class="
                    flex
                    items-center
                    px-4
                    py-2
                    mr-2
                    mb-2
                    border border-gray-200
                    rounded-sm
                    hover:border-gray-400 hover:cursor-pointer
                  "
                >
                  <div class="pr-2 mr-2 border-r border-gray-200 text-sm">
                    <!-- Numero -->
                    {{ item.number }}
                    <!-- Extensiones -->
                    <span
                      v-for="(ext, index) in item.exts"
                      :key="index"
                      class="text-gray-400"
                    >
                      - (ext: {{ ext }})
                    </span>
                  </div>

                  <a
                    href="javascript:;"
                    class="text-sm text-gray-400 hover:text-gray-700"
                    @click="removePhone(index)"
                    >x</a
                  >
                </li>
              </ul>
            </div>
          </transition>
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
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    CustomLabel,
    JetCheckbox,
    LinkButton,
  },
  props:['buildingAdmin'],
  setup(props) {
    const form = useForm({
      name: props.buildingAdmin.name,
      address: props.buildingAdmin.address,
      admin_first_name: props.buildingAdmin.admin_first_name,
      admin_last_name: props.buildingAdmin.admin_last_name,
      admin_document_number: props.buildingAdmin.admin_document_number,
      phones: props.buildingAdmin.phones,
      email: props.buildingAdmin.email,
    });

    return { form };
  },
  data() {
    return {
      phones: [],
      phoneNumber: null,
      hasExtension: false,
      ext1: null,
      ext2: null,
      admins: [],
    };
  },
  beforeMount(){
    this.phones = this.buildingAdmin.phones ? this.buildingAdmin.phones : [];
  },
  methods: {
    /**
     * Agrega la informacipon del telefono al listado.
     */
    addPhone() {
      if (this.phoneNumber) {
        //Se guarda el numero de telefono
        let phone = {
          number: this.phoneNumber,
          exts: [],
        };

        //Se agregan las extensiones
        if (this.hasExtension) {
          if (this.ext1) {
            phone.exts.push(this.ext1);
          }

          if (this.ext2) {
            phone.exts.push(this.ext2);
          }
        }

        //Se agrega el numero al formulario
        this.phones.push(phone);

        this.resetPhoneInput();
      }
    },
    /**
     * Se encarga de resetear los campos encargados
     * de ingresar el telefono al formualrio.
     */
    resetPhoneInput() {
      this.phoneNumber = null;
      this.hasExtension = false;
      this.ext1 = null;
      this.ext2 = null;
    },
    /**
     * Se encarga de remover un telefono del listado de telefonos.
     * @param number index El index del telefono a eliminar.
     */
    removePhone(index) {
      this.form.phones.splice(index, 1);
      Number;
    },
    submit() {
      let url = route("buildingAdmin.update", this.buildingAdmin.id);
      //Se no hay telefonos agregados entonces se pone el campo en null
      this.form.phones = this.phones.length ? this.phones : null;

      this.form.put(url);
    },
  },
};
</script>