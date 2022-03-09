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
        <table class="w-full border-collapse border border-slate-500 text-sm">
          <thead>
            <tr class="text-sm bg-slate-100">
              <!-- Imagen -->
              <th class="w-20 border border-slate-500 text-gray-800">
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
              <th class="border border-slate-500 text-gray-800">Dirección</th>
              <th class="border border-slate-500 text-gray-800">Propietario</th>
              <th class="border border-slate-500 text-gray-800">
                Administración
              </th>
              <th class="border border-slate-500 text-gray-800">Canon</th>
              <th class="border border-slate-500 text-gray-800">Aseguradora</th>
              <th class="border border-slate-500 text-gray-800">Resumen</th>
              <th class="border border-slate-500 text-gray-800"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in buildings" :key="item.id">
              <!-- Imagen -->
              <td class="w-20 h-20 border border-slate-500">
                <img
                  :src="item.image_url"
                  :alt="item.address?.address"
                  class="block w-full h-full object-cover object-center"
                  v-if="item.image_url"
                  loading="lazy"
                />
              </td>

              <!-- Dirección -->
              <td class="px-2 py-1 text-gray-800 border border-slate-500">
                <div class="flex flex-col items-start">
                  <!-- Dirección -->
                  <p>
                    <span class="font-bold">{{ item.address?.address }}</span>
                    {{ item.building_admin?.name }}
                    <span
                      v-if="item.building_type && item.address?.apartment"
                      class="font-bold"
                    >
                      <span v-if="item.building_type == 'apartment'"
                        >Apt.</span
                      >
                      <span v-if="item.building_type == 'business'">Local</span>
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
                  <p class="text-gray-600">
                    Codigo: <span class="font-bold">{{ item.code }}</span> -
                    <span v-if="item.available">Diponible.</span>
                    <span v-else>No disponible.</span>
                  </p>
                </div>
              </td>

              <!-- Propietario -->
              <td
                class="
                  px-2
                  py-1
                  text-gray-800
                  border border-slate-500
                  whitespace-nowrap
                "
              >
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
              <td
                class="
                  px-2
                  py-1
                  text-gray-800
                  border border-slate-500
                  whitespace-nowrap
                "
              >
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
              <td
                class="
                  px-2
                  py-1
                  text-gray-800
                  border border-slate-500
                  text-center
                "
              >
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
              <td
                class="
                  px-2
                  py-1
                  text-gray-800
                  border border-slate-500
                  whitespace-nowrap
                "
              >
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
              <td
                class="
                  px-2
                  py-1
                  text-gray-800
                  border border-slate-500
                  whitespace-nowrap
                "
              >
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
              <td class="px-2 py-1 border border-slate-500">
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
  </app-layout>
</template>

<style scoped>
.glamorous {
  background-color: #fefeff;
  background-image: url("data:image/svg+xml,%3Csvg width='180' height='180' viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M81.28 88H68.413l19.298 19.298L81.28 88zm2.107 0h13.226L90 107.838 83.387 88zm15.334 0h12.866l-19.298 19.298L98.72 88zm-32.927-2.207L73.586 78h32.827l.5.5 7.294 7.293L115.414 87l-24.707 24.707-.707.707L64.586 87l1.207-1.207zm2.62.207L74 80.414 79.586 86H68.414zm16 0L90 80.414 95.586 86H84.414zm16 0L106 80.414 111.586 86h-11.172zm-8-6h11.173L98 85.586 92.414 80zM82 85.586L87.586 80H76.414L82 85.586zM17.414 0L.707 16.707 0 17.414V0h17.414zM4.28 0L0 12.838V0h4.28zm10.306 0L2.288 12.298 6.388 0h8.198zM180 17.414L162.586 0H180v17.414zM165.414 0l12.298 12.298L173.612 0h-8.198zM180 12.838L175.72 0H180v12.838zM0 163h16.413l.5.5 7.294 7.293L25.414 172l-8 8H0v-17zm0 10h6.613l-2.334 7H0v-7zm14.586 7l7-7H8.72l-2.333 7h8.2zM0 165.414L5.586 171H0v-5.586zM10.414 171L16 165.414 21.586 171H10.414zm-8-6h11.172L8 170.586 2.414 165zM180 163h-16.413l-7.794 7.793-1.207 1.207 8 8H180v-17zm-14.586 17l-7-7h12.865l2.333 7h-8.2zM180 173h-6.613l2.334 7H180v-7zm-21.586-2l5.586-5.586 5.586 5.586h-11.172zM180 165.414L174.414 171H180v-5.586zm-8 5.172l5.586-5.586h-11.172l5.586 5.586zM152.933 25.653l1.414 1.414-33.94 33.942-1.416-1.416 33.943-33.94zm1.414 127.28l-1.414 1.414-33.942-33.94 1.416-1.416 33.94 33.943zm-127.28 1.414l-1.414-1.414 33.94-33.942 1.416 1.416-33.943 33.94zm-1.414-127.28l1.414-1.414 33.942 33.94-1.416 1.416-33.94-33.943zM0 85c2.21 0 4 1.79 4 4s-1.79 4-4 4v-8zm180 0c-2.21 0-4 1.79-4 4s1.79 4 4 4v-8zM94 0c0 2.21-1.79 4-4 4s-4-1.79-4-4h8zm0 180c0-2.21-1.79-4-4-4s-4 1.79-4 4h8z' fill='%2305f905' fill-opacity='0.3' fill-rule='evenodd'/%3E%3C/svg%3E");
}
</style>

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
  },
};
</script>