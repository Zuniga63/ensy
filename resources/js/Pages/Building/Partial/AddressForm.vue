<template>
  <jet-form-section @submitted="submit">
    <template #title> Actualizar Dirección </template>

    <template #description>
      Actualiza la información de la ubicación del inmueble.
    </template>

    <template #form>
      <!-- Grupo de Administración -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="buildign-admin"
          class="mb-2"
          value="Administración"
        />

        <!-- Campo -->
        <select
          name="buildign-admin"
          id="buildign-admin"
          v-model="form.building_admin_id"
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
          <option :value="null">No tiene administración</option>
          <option v-for="admin in admins" :key="admin.id" :value="admin.id">
            {{ admin.name }}
          </option>
        </select>

        <!-- Mensaje de error -->
        <jet-input-error
          :message="form.errors.building_admin_id"
          class="mt-2"
        />
      </div>

      <!-- Tipo de inmuebles -->
      <div class="col-span-6 lg:col-span-3">
        <div class="grid grid-cols-3 gap-2">
          <!-- Selector del tipo -->
          <div class="col-span-2">
            <!-- Etiqueta -->
            <custom-label
              for="buildign-type"
              class="mb-2"
              value="Tipo de Inmueble"
            />

            <!-- Campo -->
            <select
              name="buildign-type"
              id="buildign-type"
              v-model="form.building_type"
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
              <option :value="null">Indefinido</option>
              <option value="house">Casa</option>
              <option value="apartment">Apartamento</option>
              <option value="business">Local</option>
            </select>

            <!-- Mensaje de error -->
            <jet-input-error
              :message="form.errors.building_type"
              class="mt-2"
            />
          </div>

          <!-- Numero del local o apartamento -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-address-apartment"
              class="mb-2 transition-opacity duration-300"
              :class="{
                'text-opacity-20': !additionalAddress,
              }"
            >
              <span v-if="form.building_type === 'business'">Local</span>
              <span v-else>Apartamento</span>
            </custom-label>
            <!-- Input -->
            <jet-input
              id="building-address-apartment"
              name="building-address-apartment"
              type="number"
              v-model="address.apartment"
              class="w-full text-center text-sm text-gray-800"
              :class="{
                'border-opacity-40': !additionalAddress,
                'text-opacity-40': !additionalAddress,
              }"
              placeholder="Ej: 401"
              min="1"
              :disabled="!additionalAddress"
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.socioeconomic"
              class="mt-2"
            />
          </div>
        </div>
      </div>

      <!-- Departamento -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="buildign-department"
          class="mb-2"
          value="Departamento"
          required
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
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="buildign-town"
          class="mb-2"
          value="Municipio"
          required
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
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="buildign-district"
          class="mb-2"
          value="Barrio"
          required
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
        <jet-input-error :message="form.errors.town_district_id" class="mt-2" />
      </div>

      <!-- Dirección y apartamento/local -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="building-address"
          class="mb-2"
          value="Dirección"
          required
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="building-address"
          class="w-full text-sm"
          placeholder="Ejemplo: Calle 5 #24-34"
          v-model="address.address"
        />

        <!-- Mensaje de error -->
        <jet-input-error :message="form.errors.address" class="mt-2" />
      </div>

      <!-- Obsrvación -->
      <div class="col-span-6">
        <!-- Etiqueta -->
        <custom-label
          for="building-observation"
          class="mb-2"
          value="Observación"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="building-observation"
          class="w-full"
          placeholder="Ejemplo: Casa de color verde limón con tejado de zinc."
          v-model="address.observation"
        />

        <!-- Mensaje de error -->
        <jet-input-error :message="form.errors.observation" class="mt-2" />
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
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    JetFormSection,
    CustomLabel,
    JetInput,
    JetInputError,
    JetActionMessage,
    JetButton,
  },
  props: ["building", "departments", "allDistricts", "admins"],
  setup(props) {
    const form = useForm({
      building_admin_id: props.building.building_admin_id,
      building_type: props.building.building_type,
      country_department_id: props.building.country_department_id,
      town_id: props.building.town_id,
      town_district_id: props.building.town_district_id,
      address: null,
    });
    return { form };
  },
  data() {
    return {
      address: {
        address: null,
        observation: null,
      },
    };
  },
  beforeMount() {
    if (this.building.address) {
      this.address.address = this.building.address.address;
      this.address.observation = this.building.address.observation;
      this.address.apartment = this.building.address.apartment;
    }
  },
  methods: {
    submit() {
      let url = route("building.updateAddress", this.building.id);
      this.form.address = this.getAddress();

      this.form.put(url, {
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
     * Crea un objeto para la dirección simplificado. Elimina los pueblos en
     * el departamento seleccionado y los barrios en el municipio. Ademas solo
     * admite el id y el nombre de cada uno.
     * @return {null|object}
     */
    getAddress() {
      if (
        this.department ||
        this.town ||
        this.district ||
        this.address.address ||
        this.address.observation
      ) {
        return {
          department: {
            id: this.department?.id,
            name: this.department?.name,
          },
          town: {
            id: this.town?.id,
            name: this.town?.name,
          },
          district: {
            id: this.district?.id,
            name: this.district?.name,
          },
          address: this.address.address,
          observation: this.address.observation,
          apartment: this.additionalAddress ? this.address.apartment : null,
        };
      }

      return null;
    },
  },
  computed: {
    department() {
      if (this.form.country_department_id) {
        return this.departments.find(
          (item) => item.id === this.form.country_department_id
        );
      }

      return null;
    },
    towns() {
      if (this.department) {
        return this.department.towns;
      }

      return [];
    },
    town() {
      if (this.form.town_id) {
        return this.towns.find((item) => item.id === this.form.town_id);
      }

      return null;
    },
    districts() {
      if (this.town) {
        return this.town.districts;
      }

      return this.allDistricts;
    },
    district() {
      if (this.form.town_district_id) {
        return this.districts.find(
          (dis) => dis.id === this.form.town_district_id
        );
      }

      return null;
    },
    additionalAddress() {
      return (
        this.form.building_type === "apartment" ||
        this.form.building_type === "business"
      );
    },
  },
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
    "form.building_admin_id"(newValue, oldValue) {
      //Se recupera las instancias de las adiministraciones y la dirección.
      let newAdmin = this.admins.find((item) => item.id == newValue);
      let oldAdmin = this.admins.find((item) => item.id == oldValue);
      let address = this.address.address;

      //Se la instancia de la administración tiene valor
      if (newAdmin) {
        //Se modifica los campos si la dirección es cero o si la que tiene es
        //la misma que la de la adminsitración anterior.
        if (!address || (oldAdmin && address == oldAdmin.address)) {
          //Se establece la dirección igual a la de la adminsitración
          this.address.address = newAdmin.address;

          //Se establece lel indicador del departamento
          if (newAdmin.department)
            this.form.country_department_id = newAdmin.department.id;

          //Se establece el indicador para el pueblo
          if (newAdmin.town) {
            setTimeout(() => {
              this.form.town_id = newAdmin.town.id;
            }, 50);
          }

          //Se establece el indicador para el barrio
          if (newAdmin.district) {
            setTimeout(() => {
              this.form.town_district_id = newAdmin.district.id;
            }, 100);
          }
        }
      } else if (oldAdmin && address && address == oldAdmin.address) {
        //Si no tiene valor entoneces se resetean los cmapos solo si la direcció
        //es la misma que la de la vieja dministración.
        this.address.address = null;
        this.form.country_department_id = null;
      }
    },
  },
};
</script>