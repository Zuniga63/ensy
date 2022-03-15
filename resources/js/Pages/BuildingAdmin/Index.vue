<template>
  <app-layout title="administraciones">
    <template #header>
      <div class="flex justify-between w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Administraciones
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('buildingAdmin.create')">
          Registrar Administración
        </link-button>
      </div>
    </template>

    <!-- Body -->
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="py-5 px-4 rounded-md bg-white shadow">
        <!-- Controles de busqueda -->
        <div class="grid grid-cols-2 gap-4 w-full mb-4">
          <!-- Busqueda por nombre -->
          <div class="flex flex-col">
            <custom-label
              for="searchByName"
              value="Nombre del Edificio"
              class="uppercase mb-2 text-sm"
            />
            <jet-input
              type="text"
              id="searchByName"
              placeholder="Escribe el nombre del edificio."
              class="w-full text-sm"
              v-model="search"
            />
          </div>
        </div>

        <!-- Tabla con los datos de los clientes -->
        <div class="h-[28rem] shadow border-b border-gray-300 overflow-y-auto">
          <table class="relative min-w-full table-auto">
            <thead class="sticky top-0 bg-gray-50">
              <tr>
                <!-- INDEX -->
                <th
                  scope="col"
                  class="
                    px-4
                    py-3
                    text-center text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  #
                </th>
                <!-- Nombres -->
                <th
                  scope="col"
                  class="
                    px-4
                    py-3
                    text-left text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Nombre
                </th>

                <!-- Administrador -->
                <th
                  scope="col"
                  class="
                    px-4
                    py-3
                    text-center text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Administrador
                </th>

                <!-- Telefono -->
                <th
                  scope="col"
                  class="
                    px-4
                    py-3
                    text-center text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Telefono
                </th>

                <!-- Inmuebles -->
                <th
                  scope="col"
                  class="
                    px-4
                    py-3
                    text-center text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  <div class="flex justify-center">
                    <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class=" h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                  </svg>
                  </div>
                </th>

                <!-- Telefono -->
                <th
                  scope="col"
                  class="
                    px-4
                    py-3
                    text-center text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Tarifas
                </th>

                <th scope="col" class="relative px-4 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="(admin, index) in adminList"
                :key="admin.id"
                class="transition-colors hover:bg-indigo-50"
              >
                <!-- Index -->
                <td class="px-2 py-2 text-gray-800 text-center text-gray-800">
                  {{ index + 1 }}
                </td>

                <!-- Nombre del Conjunto -->
                <td class="px-2 py-2 text-gray-800 whitespace-nowrap">
                  <div class="flex flex-col">
                    <p>{{ admin.name }}</p>
                    <!-- Address -->
                    <p class="text-sm text-gray-400">
                      <!-- DIrección -->
                      <span v-if="admin.address" class=""
                        >{{ admin.address }}
                      </span>
                      <!-- Barrio -->
                      <span v-if="admin.district">
                        &nbsp; Barrio {{ admin.district.name }}
                      </span>
                    </p>
                    <!-- Town and Department -->
                    <p class="text-sm text-gray-400" v-if="admin.town">
                      <span class="text-xs"> {{ admin.town.name }} </span>
                      <span v-if="admin.department">
                        &#32;&#40;{{ admin.department.name }} &#41;</span
                      >
                    </p>
                    <!-- /.end address -->
                  </div>
                </td>

                <!-- Administrador -->
                <td class="px-2 py-2 text-gray-800 whitespace-nowrap">
                  <div
                    class="flex flex-col justify-center items-center"
                    v-if="admin.full_name"
                  >
                    <!-- Name -->
                    <p>{{ admin.full_name }}</p>
                    <!-- Email -->
                    <p class="text-sm text-gray-600" v-if="admin.email">
                      {{ admin.email }}
                    </p>
                    <!-- Document -->
                    <span
                      class="text-sm text-gray-400 tracking-widest"
                      v-if="admin.admin_document_number"
                    >
                      CC:
                      <span>{{
                        formatDocument(admin.admin_document_number)
                      }}</span>
                    </span>
                  </div>
                  <p v-else class="text-gray-400 text-center">No Registrado.</p>
                </td>

                <!-- Telefono -->
                <td class="px-2 py-2 text-gray-800">
                  <div v-if="admin.phones" class="flex flex-col items-center">
                    {{ formatPhone(admin.phones[0].number) }}
                    <div>
                      <span
                        v-for="(ext, index) in admin.phones[0].exts"
                        :key="index"
                        class="text-gray-400 text-sm"
                      >
                        (ext: {{ ext }})
                      </span>
                    </div>
                  </div>
                  <div v-else class="text-center">No registrado.</div>
                </td>

                <!-- Inmuebles -->
                <td class="px-2 py-2 text-center text-gray-800">
                  {{ admin.buildings_count }}
                </td>

                <!-- Tarifas -->
                <td class="px-2 py-2 text-gray-800 text-center">
                  {{ formatCurrency(admin.fees) }}
                </td>

                <!-- Acciones -->
                <td class="px-2 py-2">
                  <div class="flex justify-end">
                    <row-button
                      type="show"
                      class="hidden mr-2"
                      :href="route('customer.index')"
                      title="Ver Cliente"
                    />
                    <row-button
                      type="edit"
                      class="mr-2"
                      title="Editar Cliente"
                      :href="route('buildingAdmin.edit', admin.id)"
                    />
                    <row-button
                      type="delete"
                      @click="deleteAdmin(admin)"
                      title="Eliminar Cliente"
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
  props: {
    admins: Array,
  },
  data() {
    return {
      search: null,
    };
  },
  methods: {
    deleteAdmin(admin) {
      let url = route("buildingAdmin.destroy", admin.id);
      let message = `Esta acción es irreversible `;
      message += "y eliminará al grupo de administracion ";
      message += `<b>${admin.name}</b> y tambien las relaciones con los inmuebles.`;

      Swal.fire({
        title: "¿Desea Eliminar esta administración?",
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
              title: `Administración Eliminada!`,
              text: `La administración del edificio ${res.admin.name} fue eliminada`,
            });

            this.removeAdmin(res.admin.id);
          } else {
            this.showError(res, admin);
            //Se refrescan los datos de los clientes
            this.$inertia.reload({
              only: ["admins"],
            });
          }
        }
      });
    },
    removeAdmin(adminId) {
      //Se busca el index del cliente
      let index = this.admins.findIndex((adm) => adm.id === adminId);
      this.admins.splice(index, 1);
    },
    /**
     * Se encarga de mostrar el mensaje de error al
     * usuario de la plataforma.
     */
    async showError(error, admin) {
      let title = "¡Opps, algo salio mal!";
      let icon = "error";
      let message = null;

      if (error.status == 404) {
        //Se redacta el mensaje a mostrar
        message = "La administracion ";
        message += `<b>${admin.name}</b> no fue encontrado.`;
      } else {
        message = `La Administración <b>${admin.name}</b> `;
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
    filterByName(name, list) {
      name = this.normalizeString(name);
      return list.filter((item) => {
        let itemName = this.normalizeString(item.name);
        return itemName.includes(name);
      });
    },
    /**
     *  Se encarga de formatear el numero a moneda
     * @param {string} numero a formatear.
     */
    formatCurrency(number) {
      let fractionDigits = 0;
      let style = "currency";
      let currency = "COP";

      const formater = new Intl.NumberFormat("es-CO", {
        style,
        currency,
        minimumFractionDigits: fractionDigits,
      });

      return formater.format(number);
    },
    /**
     * Se encarga de poner un punto cada tercer numero
     * de un documento
     * @param {string} document numero de documento
     * @return {string}
     */
    formatDocument(document) {
      //Se recupera cada elemento de forma inversa
      /**@type {array} */
      let elements = document.split("").reverse();
      let result = [];

      elements.forEach((item, index) => {
        let count = index + 1;
        result.push(item);

        if (count % 3 === 0) result.push(".");
      });

      return result.reverse().join("");
    },
    /**
     * Se encarga de dar un formato basico ### ### ####
     * a los numeros de telefono
     */
    formatPhone(phone) {
      let elements = phone.split("").reverse();
      let result = [];

      elements.forEach((element, index) => {
        let count = index + 1;
        let count2 = count - 4;

        result.push(element);

        if (count == 4 || (count > 4 && count2 % 3 == 0)) {
          result.push(" ");
        }
      });

      return result.reverse().join("");
    },
  },
  computed: {
    adminList() {
      if (this.search) {
        return this.filterByName(this.search, this.admins);
      }

      return this.admins;
    },
  },
};
</script>