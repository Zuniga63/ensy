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
          Se va a actualizar la información del grupo de administracion
          <span class="font-bold">{{ buildingAdmin.name }}</span
          >.
        </template>

        <template #form>
          <input-group title="Ubicación" class="col-span-6">
            <div class="grid grid-cols-2 gap-4 p-1">
              <!-- Nombre del edificio -->
              <div class="relative col-span-2 mt-3">
                <!-- Etiqueta -->
                <custom-label
                  for="admin-building-name"
                  class="
                    absolute
                    top-0
                    left-4
                    px-4
                    border border-gray-200
                    rounded-full
                    bg-white
                    text-gray-400 text-xs
                    transform
                    -translate-y-1/2
                  "
                  value="Nombre del Edificio"
                  required
                />
                <!-- Input -->
                <jet-input
                  id="admin-building-name"
                  name="admin-building-name"
                  type="text"
                  v-model="form.name"
                  class="w-full pt-4"
                  placeholder="Escribe el nombre del edificio aquí."
                  ref="input"
                />

                <!-- Error -->
                <jet-input-error :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Departamento -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-department"
                  class="mb-2"
                  value="Departamento"
                />

                <!-- Campo -->
                <select
                  name="buildign-department"
                  id="buildign-department"
                  v-model="form.country_department_id"
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
                  <option :value="null">Selecciona un Departamento</option>
                  <option
                    v-for="department in departments"
                    :key="department.id"
                    :value="department.id"
                  >
                    {{ department.name }} ({{ department.towns.length }}
                    municipios)
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.country_department_id"
                  class="mt-2"
                />
              </div>

              <!-- Municipio -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-town"
                  class="mb-2"
                  value="Municipio"
                />

                <!-- Campo -->
                <select
                  name="buildign-town"
                  id="buildign-town"
                  v-model="form.town_id"
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
                  <option :value="null">Selecciona un Municipio</option>
                  <option v-for="town in towns" :key="town.id" :value="town.id">
                    {{ town.name }} ({{ town.districts.length }}
                    barrios)
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error :message="form.errors.town_id" class="mt-2" />
              </div>

              <!-- Barrio -->
              <div class="col-span-2 lg:col-span-1">
                <!-- Etiqueta -->
                <custom-label
                  for="buildign-district"
                  class="mb-2"
                  value="Barrio"
                />

                <!-- Campo -->
                <select
                  name="buildign-district"
                  id="buildign-district"
                  v-model="form.town_district_id"
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
                  <option :value="null">Selecciona un Barrio</option>
                  <option
                    v-for="district in districts"
                    :key="district.id"
                    :value="district.id"
                  >
                    {{ district.name }} ({{ district.town.name }})
                  </option>
                </select>

                <!-- Mensaje de error -->
                <jet-input-error
                  :message="form.errors.town_district_id"
                  class="mt-2"
                />
              </div>

              <!-- Dirección de la administración -->
              <div class="col-span-2 lg:col-span-1">
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
            </div>
          </input-group>

          <input-group title="Información del Administrador" class="col-span-6">
            <div class="grid grid-cols-6 gap-4">
              <!-- Nombre del administrador -->
              <div class="col-span-6 lg:col-span-2">
                <!-- Etiqueta -->
                <custom-label for="adfirst_name" class="mb-2" value="Nombres" />
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
                  value="Apellidos"
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
                  value="N° de Documento"
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
            </div>
          </input-group>
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
import InputGroup from "@/Components/Form/InputGroupSection.vue";
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
    InputGroup,
  },
  props: ["buildingAdmin", "departments", "allDistricts"],
  setup(props) {
    const form = useForm({
      name: props.buildingAdmin.name,
      country_department_id: props.buildingAdmin.country_department_id,
      town_id: props.buildingAdmin.town_id,
      town_district_id: props.buildingAdmin.town_district_id,
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
  beforeMount() {
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
  }, //.end method
  computed: {
    /**
     * Recupera la instancia del departamento
     * seleccionada.
     * @return {object|null}
     */
    department() {
      if (this.form.country_department_id) {
        return this.departments.find(
          (item) => item.id === this.form.country_department_id
        );
      }

      return null;
    },
    /**
     * Se encarga de recuperar la lista de muicipios
     * a partir del departamento creado
     * @return {array} listado de municipios
     */
    towns() {
      if (this.department) {
        return this.department.towns;
      }

      return [];
    },
    /**
     * Recupera la isntancia del municipio seleccionada
     * @return {object | null}
     */
    town() {
      if (this.form.town_id) {
        return this.towns.find((item) => item.id === this.form.town_id);
      }

      return null;
    },
    /**
     * Recupera el listado de barrios del municipio seleccionado.
     * @return {array}
     */
    districts() {
      if (this.town) {
        return this.town.districts;
      }

      return this.allDistricts;
    },
    /**
     * Recupera la instancia del barrio seleccionado
     * @return {object|null}
     */
    district() {
      if (this.form.town_district_id) {
        return this.districts.find(
          (dis) => dis.id === this.form.town_district_id
        );
      }

      return null;
    },
  }, //.end computed
  watch: {
    "form.country_department_id"(newValue) {
      //Se define si el pueblo está en el listado del departamento.
      let departmentHasTown = this.towns.some(
        (item) => item.id === this.form.town_id
      );

      /**
       * Si el departamento es null, el municipio es null.
       * Si el departamento no es null y el municipio no está en el listado
       * entonces el municipio es null.
       */
      if (!newValue || (!departmentHasTown && newValue)) {
        this.form.town_id = null;
      }
    },
    "form.town_id"(newValue) {
      let townHasDistrict = this.districts.some(
        (item) => item.id === this.form.town_district_id
      );

      /**
       * Se define el si se resetea el valor del distrito
       * Si el municipio no tiene valor, el distrito es null
       * Si el municipio tiene valor pero no esta el distrito, entonces es null
       */
      if (!newValue || (!townHasDistrict && newValue)) {
        this.form.town_district_id = null;
      }
    },
    "form.town_district_id"(newValue) {
      //Se actualizan los ID del departamento y del municipio si estas no tienen asignado uno
      if (
        (!this.form.town_id || !this.form.country_department_id) &&
        newValue
      ) {
        //Se busca la instancia del barrio en cuestion
        let district = this.allDistricts.find((item) => item.id === newValue);
        this.form.country_department_id = district.town.country_department_id;
        setTimeout(() => {
          this.form.town_id = district.town.id;
        }, 50);
      }
    },
  },//.end watch
};
</script>