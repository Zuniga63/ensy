<template>
  <app-layout title="Inmuebles">
    <template #header>
      <div class="flex justify-between w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Inmuebles
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('building.create')">
          Registrar Inmueble
        </link-button>
      </div>
    </template>

    <!-- Body -->
    <div class="max-w-full mx-auto py-10 sm:px-6 lg:px-8">
      <div class="py-5 px-4 rounded-md bg-white shadow">
        <!-- Controles de busqueda -->
        <div class="grid grid-cols-4 gap-4 w-full mb-4">
          <!-- Busqueda por Dirección -->
          <div class="flex flex-col">
            <jet-input
              type="text"
              id="searchByAddress"
              placeholder="Buscar por dirección."
              class="w-full text-sm"
              v-model="address"
            />
          </div>
          <!-- Codigo -->
          <div class="flex flex-col">
            <jet-input
              type="number"
              id="searchByCodde"
              placeholder="Buscar por codigo."
              class="w-full text-sm"
              v-model.number="code"
            />
          </div>
        </div>
        <div class="h-[28rem] border border-slate-500 rounded overflow-auto">
          <table class="relative w-full border-collapse text-sm table-sticky">
            <thead
              class="sticky top-0 p-0 ring-1 ring-slate-500 ring-opacity-100"
            >
              <tr class="text-sm bg-slate-100 divide-x divide-slate-500">
                <!-- Imagen -->
                <th class="w-20 p-0 text-gray-800">
                  <div class="flex justify-center">
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
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                  </div>
                </th>
                <th class="p-0 text-gray-800">Dirección</th>
                <th class="p-0 text-gray-800">Propietario</th>
                <th class="p-0 text-gray-800">Administración</th>
                <th class="p-0 text-gray-800">Canon</th>
                <th class="p-0 text-gray-800">Aseguradora</th>
                <th class="p-0 text-gray-800">Resumen</th>
                <th class="p-0 text-gray-800"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-500">
              <tr
                v-for="item in buildingList"
                :key="item.id"
                class="divide-x divide-slate-500 border-b border-slate-500"
              >
                <!-- Imagen -->
                <td class="w-20 py-2 px-1">
                  <div class="flex flex-col items-center">
                    <div
                      class="w-20 h-20 mb-2 overflow-hidden rounded-full"
                      v-if="item.image_url"
                    >
                      <img
                        :src="item.image_url"
                        :alt="item.address?.address"
                        class="block w-full h-full object-cover object-center"
                        loading="lazy"
                      />
                    </div>
                    <p class="text-gray-400">
                      Código:
                      <span class="font-bold">{{ item.code }}</span>
                    </p>
                  </div>
                </td>

                <!-- Dirección -->
                <td class="px-2 py-1 text-gray-800">
                  <div
                    class="flex flex-col items-center justify-start text-center"
                  >
                    <!-- Dirección -->
                    <p>
                      <!-- Dirección fisica -->
                      <span class="font-bold">{{ item.address?.address }} </span>&nbsp;
                      <span class="italic"> {{ item.building_admin?.name }} </span>&nbsp; 
                      <!-- Apartamento o Local -->                 
                      <span
                        v-if="item.building_type && item.address?.apartment"
                        class="font-bold"
                      >
                        <span v-if="item.building_type == 'apartment'"
                          >Apt.</span
                        >
                        <span v-if="item.building_type == 'business'"
                          >Local</span
                        >
                        {{ item.address.apartment }}
                      </span>
                    </p>
                    <!-- Barrio -->
                    <p>
                      Barrio
                      <span class="font-bold">
                        {{ item.address?.district.name }}
                        (<span class="text-xs">
                          {{ item.address?.town.name }} </span
                        >)
                      </span>
                    </p>
                    <!-- Observación -->
                    <span class="text-gray-400">
                      {{ item.address?.observation }}
                    </span>
                    <!-- Codigo de Archivo -->
                    <p>
                      <span v-if="item.available" class="text-green-500">Diponible.</span>
                      <span v-else class="text-gray-400">No disponible.</span>
                    </p>
                  </div>
                </td>

                <!-- Propietario -->
                <td class="px-2 py-1 text-gray-800 whitespace-nowrap">
                  <div class="flex flex-col items-center" v-if="item.owner">
                    <!-- Nombre del propietario -->
                    <span> {{ item.owner.full_name }} </span>
                    <!-- Documento de identificaicón -->
                    <p
                      class="text-xs text-gray-400"
                      v-if="item.owner.document_number"
                    >
                      <span> {{ item.owner.document_type }} : </span>
                      <span> {{ item.owner.document_number }} </span>
                    </p>
                    <!-- Email -->
                    <span v-if="item.owner.email" class="text-gray-400 text-xs">
                      {{ item.owner.email }}
                    </span>
                    <!-- Telefonos -->
                    <p
                      v-if="item.owner.contacts?.length"
                      class="text-gray-400 tracking-widest"
                    >
                      <span> {{ item.owner.contacts[0].number }} </span>
                      <span v-if="item.owner.contacts?.length > 1">
                        - {{ item.owner.contacts[1].number }}
                      </span>
                    </p>
                    <span class="mb-1 font-bold">
                      {{ calculateOwnerFee(item.lease_fee, item.commission) }}
                    </span>
                  </div>
                  <span v-else class="block text-center">No Reg.</span>
                </td>

                <!-- Administración -->
                <td class="px-2 py-1 text-gray-800 whitespace-nowrap">
                  <div
                    class="flex flex-col items-center"
                    v-if="item.building_admin"
                  >
                    <span> {{ item.building_admin.name }} </span>
                    <span> {{ item.building_admin.full_name }} </span>
                    <span
                      v-if="item.building_admin.email"
                      class="text-gray-400 text-xs"
                    >
                      {{ item.building_admin.email }}
                    </span>
                    <p
                      v-if="item.building_admin.phones?.length"
                      class="text-gray-400 tracking-widest"
                    >
                      <span> {{ item.building_admin.phones[0].number }} </span>
                      <span v-if="item.building_admin.phones?.length > 1">
                        - {{ item.building_admin.phones[1].number }}
                      </span>
                    </p>

                    <span class="font-bold">
                      {{ formatCurrency(item.admin_fee) }}
                    </span>
                  </div>
                  <span v-else class="block text-center">No Reg.</span>
                </td>

                <!-- Canon -->
                <td class="px-2 py-1 text-gray-800 text-center">
                  <div class="flex flex-col items-center">
                    <!-- Canon -->
                    <span class="font-bold mb-1">
                      {{ formatCurrency(item.lease_fee) }}
                    </span>
                    <span class="text-gray-400 text-xs">Comisión</span>
                    <span class="italic">
                      {{ calculateCommission(item.lease_fee, item.commission) }}
                    </span>
                    <span class="text-xs text-green-500">
                      ({{ printPercentage(item.commission) }})
                    </span>
                  </div>
                </td>

                <!-- Seguro -->
                <td class="px-2 py-1 text-gray-800 whitespace-nowrap">
                  <div class="flex flex-col items-center">
                    <!-- Nombre del propietario -->
                    <span> {{ item.insurance_carrier }} </span>
                    <span class="mb-1"> {{ item.insurance_type }} </span>
                    <span class="text-gray-400 text-xs">Comisión</span>
                    <span class="font-bold">
                      {{
                        calculateCommission(
                          item.lease_fee,
                          item.insurance_commission
                        )
                      }}
                    </span>
                    <span class="text-xs text-green-400">
                      ({{ printPercentage(item.insurance_commission) }})
                    </span>
                  </div>
                </td>

                <!-- Resumen -->
                <td class="px-2 py-1 text-gray-800 whitespace-nowrap">
                  <div class="flex flex-col items-center">
                    <span class="text-gray-400 text-xs">Total Recaudar</span>
                    <span class="mb-1">
                      {{ calculateCollection(item.lease_fee, item.admin_fee) }}
                    </span>
                    <span class="text-gray-400 text-xs">Utilidad</span>
                    <span class="mb-1 text-green-500">
                      {{
                        calculateUtility(
                          item.lease_fee,
                          item.commission,
                          item.insurance_commission
                        )
                      }}
                    </span>
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-2 py-1">
                  <div class="flex flex-col items-center">
                    <row-button
                      type="edit"
                      class="mb-1"
                      title="Editar Inmueble"
                      :href="route('building.edit', item.id)"
                    />

                    <row-button
                      type="delete"
                      @click="deleteBuilding(item)"
                      title="Eliminar Inmueble"
                    />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import RowButton from "@/Components/Table/RowButton.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetInput from "@/Jetstream/Input.vue";

import Swal from "sweetalert2";
import axios from "axios";

export default {
  components: {
    AppLayout,
    JetButton,
    LinkButton,
    RowButton,
    CustomLabel,
    JetInput,
  },
  props: ["buildings"],
  setup(props) {
    let fractionDigits = 0;
    let style = "currency";
    let currency = "COP";

    let formater = new Intl.NumberFormat("es-CO", {
      style,
      currency,
      minimumFractionDigits: fractionDigits,
    });

    return { formater };
  },
  data() {
    return {
      address: null,
      code: null,
    };
  },
  methods: {
    /**
     * Convierte la cadena de texto con la fracción
     * y le da un formato de porcentahe
     * @param {string} base La base de la comissión
     * @param {string} commission La comissión en fracción
     * @return {string}
     */
    printPercentage(commission) {
      commission = parseFloat(commission);
      return `${Math.round(commission * 100)}%`;
    },
    formatCurrency(number) {
      return this.formater.format(number);
    },
    calculateCommission(base, commission) {
      commission = parseFloat(commission);
      base = parseFloat(base);

      return this.formatCurrency(base * commission);
    },
    calculateCollection(leaseFee, adminFee) {
      leaseFee = parseFloat(leaseFee);
      adminFee = parseFloat(adminFee);

      return this.formatCurrency(leaseFee + adminFee);
    },
    calculateOwnerFee(leaseFee, commission) {
      leaseFee = parseFloat(leaseFee);
      commission = parseFloat(commission);

      return this.formatCurrency(leaseFee - leaseFee * commission);
    },
    calculateUtility(leaseFee, commission, insuranceCommissión) {
      leaseFee = parseFloat(leaseFee);
      commission = parseFloat(commission);
      insuranceCommissión = parseFloat(insuranceCommissión);

      let utility = leaseFee * commission - leaseFee * insuranceCommissión;

      return this.formatCurrency(utility);
    },
    /**
     * Muestra el modal donde le solicita al usuario
     * que confirme la eliminación del cliente.
     */
    deleteBuilding(building) {
      //Url de la petición
      let url = route("building.destroy", building.id);

      //Mensaje para la ventana modal.
      let message = `Esta acción es irreversible `;
      message += "y eliminará todos los datos del inmueble.";

      //Ventana modal sweetalert
      Swal.fire({
        title: "¿Desea Eliminar este inmueble?",
        html: message,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Si, eliminalo!",
        cancelButtonText: "Cancelar",
        showLoaderOnConfirm: true,
        preConfirm: async () => {
          try {
            const res = await axios.delete(url);
            return res.data;
          } catch (error) {
            return {
              ok: false,
              status: error.response.status,
              statusText: error.response.statusText,
            };
          }
        },
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
      }).then(async (result) => {
        //Los datos provenientes del servidor.
        let res = result.value;

        if (result.isConfirmed) {
          if (res.ok) {
            Swal.fire({
              title: `¡Inmueble Eliminado!`,
              icon: "success",
              text: `El inmueble con ID:${res.building.id} fue eliminado`,
            });

            this.removeBuilding(res.building.id);
          } else {
            this.showError(res, customer);
            //Se refrescan los datos de los clientes
            this.$inertia.reload({
              only: ["customers"],
            });
          }
        }
      });
    },
    /**
     * Se encarga de remover la instancia del cliente
     * que ya fue removida de la base de datos.
     */
    removeBuilding(buildingId) {
      //Se busca el index del cliente
      let index = this.buildings.findIndex((b) => b.id === buildingId);
      this.buildings.splice(index, 1);
    },
    /**
     * Se encarga de mostrar el mensaje de error al
     * usuario de la plataforma.
     */
    async showError(error, buildign) {
      let title = "¡Opps, algo salio mal!";
      let icon = "error";
      let message = null;

      if (error.status == 404) {
        //Se redacta el mensaje a mostrar
        message = "El inmueble ";
        message += `con ID:<b>${building.id}</b> no fue encontrado.`;
      } else {
        message = `EL inmueble con ID:<b>${building.id}</b> `;
        message += "no existe o no puede ser eliminado. ";
        message += "Por favor Contacte con el administrador.";
      }

      Swal.fire({
        title,
        icon,
        html: message,
      });
    },
    /**
     * Este metodo se encarga de llevar a minusculas el texto
     * pasado como parametro y remover de forma segura los guiños como
     * las eñes.
     * @param String text cadena de texto a normalizar.
     */
    normalizeString(text) {
      return text
        ? text
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
        : null;
    },
    /**
     * Filtra los clientes por su nombre
     * @param {String} address Es el nombre que se va a filtrar
     * @param {Array} list Listado a filtrar.
     * @returns Array
     */
    filterByAddress(address, list) {
      address = this.normalizeString(address);
      return list.filter((item) => {
        if (item.address.address) {
          let itemAddress = this.normalizeString(item.address.address);
          return itemAddress.includes(address);
        }

        return false;
      });
    },
    /**
     * Filtra los inmuebles por el codigo
     * @param {number} code
     * @param {Array} list Listado a filtrar.
     */
    filterByCode(code, list) {
      console.log(code, typeof list[0].code, list[0].code)
      return list.filter((item) => item.code === code);
    },
  },
  computed: {
    buildingList() {
      let result = this.buildings;
      if(this.code) result = this.filterByCode(this.code, result);
      if (this.address) result = this.filterByAddress(this.address, result);
      return result;
    },
  },
};
</script>