<template>
  <jet-form-section @submitted="submit">
    <template #title> Actualizar Dirección </template>

    <template #description>
      Actualiza la información de la ubicación del inmueble.
    </template>

    <template #form>
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

      <!-- Dirección -->
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
          class="w-full"
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
  props: ["building", "departments", "allDistricts"],
  setup(props) {
    const form = useForm({
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
  },
};
</script>