<template>
  <JetFormSection @submitted="submit">
    <template #title> Registro de Barrios </template>

    <template #description>
      En está sección se puede visualizar los barrios que actualmente están
      registrados en la base de datos. De la misma forma tambien se pueden
      registrar nuevos barrios.
    </template>

    <template #form>
      <!-- Seleccionar departamento -->
      <div class="col-span-3">
        <Select2
          v-model="form.department_id"
          :options="departmentList"
          :settings="{
            width: '100%',
            selectOnClose: true,
          }"
          class="font-bold"
          placeholder="Selecciona un departamento."
        />

        <JetInputError
          :message="form.errors.department_id"
          class="mt-1 ml-2"
        ></JetInputError>
      </div>
      <!-- Seleccionar municipio -->
      <div class="col-span-3">
        <Select2
          v-model="form.town_id"
          :options="townList"
          :settings="{
            width: '100%',
          }"
          class="font-bold"
          placeholder="Selecciona un municipio."
        />

        <JetInputError
          :message="form.errors.town_id"
          class="mt-2 ml-2"
        ></JetInputError>
      </div>

      <!-- BARRIO Y CONTROLES -->
      <div class="col-span-6 grid grid-cols-12 gap-2">
        <!-- CAMPO PARA RGISTRAR NOMBRE DEL BARIO -->
        <div
          :class="{
            'col-span-12': !showCancelButton,
            'col-span-11': showCancelButton,
          }"
        >
          <!-- INPUT PARA EL NOMBRE -->
          <JetInput
            type="text"
            class="w-full"
            v-model="form.name"
            placeholder="Escribe el nombre del barrio."
          ></JetInput>

          <!-- ERROR EN EL NOMBRE DEL BARRIO -->
          <JetInputError
            :message="form.errors.name"
            class="mt-1 ml-2"
          ></JetInputError>

          <!-- ERROR EN EL IDENTIFICADOR DEL BARRIO -->
          <JetInputError
            :message="form.errors.district_id"
            class="mt-1 ml-2"
          ></JetInputError>
        </div>
        <!-- BOTON PARA CANCELAR PROCESO -->
        <a
          href="javascript:;"
          @click="cancel"
          class="
            self-start
            justify-self-center
            p-2
            border
            rounded
            hover:ring hover:ring-red-500 hover:ring-opacity-50
            border-red-500
            bg-red-200
            hover:bg-red-50
            text-red-600
            transition-colors
            duration-150
            shadow
          "
          :class="{
            hidden: !showCancelButton,
            'col-span-1': showCancelButton,
          }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </a>
      </div>

      <!-- Listado de barrios -->
      <div class="col-span-6">
        <ul
          class="
            h-60
            p-4
            mb-1
            border border-gray-200
            rounded
            bg-gray-50
            shadow
            overflow-y-auto
            snap-y
          "
        >
          <li
            v-for="district in town?.districts"
            :key="district.id"
            class="
              px-4
              py-2
              border border-indigo-600
              rounded
              mb-2
              hover:ring hover:ring-indigo-700 hover:ring-opacity-50
              transition-colors
              duration-100
              scroll-mt-4
            "
            :class="{
              'bg-white': district.id !== form.district_id,
              'bg-indigo-500 bg-opacity-10': district.id === form.district_id,
            }"
          >
            <div class="flex justify-between items-center">
              <!-- District Name -->
              <p class="text-base text-indigo-800 tracking-wider font-bold">
                {{ district.name }}
              </p>

              <!-- Controles -->
              <div class="flex">
                <!-- Editar -->
                <a
                  href="javascript:;"
                  class="
                    p-2
                    border border-blue-500
                    mr-2
                    rounded
                    bg-blue-100
                    hover:bg-blue-50
                    transition-colors
                    duration-200
                    text-blue-700
                    shadow
                  "
                  @click="editDistrict(district)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                    />
                    <path
                      fill-rule="evenodd"
                      d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </a>

                <!-- Eliminar -->
                <a
                  href="javascript:;"
                  @click="confirmDelete(district)"
                  class="
                    p-2
                    border border-red-500
                    rounded
                    bg-red-200
                    hover:bg-red-50
                    transition-colors
                    duration-200
                    text-red-700
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </a>
              </div>
            </div>
          </li>
        </ul>
        <p class="mr-4 text-sm text-gray-800 text-opacity-70 text-right">
          Barrios: <span class="font-bold">{{ town?.districts.length }}</span>
        </p>
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
        <span v-show="type === types.CREATE">Guardar</span>
        <span v-show="type === types.UPDATE">Actualizar</span>
      </jet-button>
    </template>
  </JetFormSection>
</template>

<script>
import JetButton from "@/Jetstream/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import Select2 from "vue3-select2-component";

import { useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import Swal from "sweetalert2";

const FormType = {
  UPDATE: 1,
  CREATE: 2,
};
Object.freeze(FormType);

export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
    Select2,
  },

  props: {
    departments: {
      type: Array,
      default: [],
    },
  },
  setup(props) {
    const form = useForm({
      department_id: null,
      town_id: null,
      district_id: null,
      name: null,
    });

    return { form };
  },
  data() {
    return {
      types: FormType,
      type: FormType.CREATE,
      myValue: "",
    };
  },
  methods: {
    /**
     * Se encarga de resetear el estado del
     * formulario para el registro de nuevo barrio.
     */
    cancel() {
      this.form.reset("district_id", "name");
      this.type = FormType.CREATE;
    },
    /**
     * Se encarga de montar los datos del distrito
     * en el formulario.
     */
    editDistrict(district) {
      this.form.district_id = district.id;
      this.form.name = district.name;
      this.type = FormType.UPDATE;
    },
    /**
     * Metodo encargado de gestionar las
     * peticiones del formulario.
     */
    submit() {
      let url = null;
      let options = {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.cancel();
        },
      };

      if (this.type === FormType.CREATE) {
        url = route("config.storeTownDistrict");
        this.form.post(url, options);
      } else if (this.type === FormType.UPDATE) {
        url = route("config.updateTownDistrict");
        this.form.put(url, options);
      }
    },
    /**
     * Le pregunta al usuario si desea
     * eliminar el recurso de la base de datos.
     */
    confirmDelete(district) {
      let message = "Esta acción es irreversible y el barrio ";
      message += `<b>${district.name}</b> `;
      message += "será eliminado de la base de datos. ";
      message +=
        "Todas las relaciones establecidas pasarán a ser desconocidas.";

      Swal.fire({
        title: "¿Deseas eliminar este barrio?",
        icon: "warning",
        html: message,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Si, Eliminala!",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteDistrict(district);
        }
      });
    },
    deleteDistrict(district) {
      let urlName = "config.destroyTownDistrict";
      let url = route(urlName);
      Inertia.delete(url, {
        preserveScroll: true,
        preserveState: true,
        data: district,
        onSuccess: (page) => {
          let res = page.props.flash.message;

          if (res) {
            let message = `El barrio <b>${res.district.name}</b>`;
            let icon = "success";
            let title = "¡Barrio Eliminado!";

            if (res.ok) {
              message += " se eliminó correctamente.";
            } else {
              title = "¡Oops, algo pasó!";
              message += " ya fue eliminado.";
              icon = "info";
            }

            Swal.fire({
              title,
              icon,
              html: message,
            });
          }
        },
      });
    },
  },
  computed: {
    /**
     * Define si se muestra u oculta el boton
     * para resetear formulario de creación o actualización.
     */
    showCancelButton() {
      return this.form.name || this.type === FormType.UPDATE;
    },
    /**
     * Se encarga de crear la lista de departamentos que usa el
     * select3 de vue
     */
    departmentList() {
      let list = [];
      this.departments.forEach((item) => {
        list.push({
          id: item.id,
          text: `${item.name} (${item.towns.length} municipios)`,
        });
      });

      return list;
    },
    /**
     * Se encarga de montar en memoria la instancia del departamento
     * seleccionado.
     */
    department() {
      let id = parseInt(this.form.department_id);
      if (!isNaN(id)) {
        return this.departments.find((item) => item.id === id);
      }

      return null;
    },
    townList() {
      let list = [];
      if (this.department) {
        this.department.towns.forEach((item) => {
          list.push({
            id: item.id,
            text: `${item.name} (${item.districts.length} barrios)`,
          });
        });
      }

      return list;
    },
    town() {
      let id = parseInt(this.form.town_id);
      if (!isNaN(id) && this.department) {
        return this.department.towns.find((item) => item.id === id);
      }

      return null;
    },
  },
  watch: {
    department(newValue, oldValue){
      if(!newValue && (oldValue && newValue.id !== oldValue.id)){
        this.form.town_id = null;
      }
    }
  },
};
</script>