<template>
  <app-layout title="Clientes">
    <template #header>
      <div class="flex justify-between w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Administración de Clientes
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('customer.create')">
          Registrar Cliente
        </link-button>
      </div>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="py-5 px-4 rounded-md bg-white shadow">
        <!-- Controles de busqueda -->
        <div class="grid grid-cols-2 gap-4 w-full mb-4">
          <!-- Busqueda por nombre -->
          <div class="flex flex-col">
            <custom-label
              for="searchByName"
              value="Buscar por nombre"
              class="uppercase mb-2 text-sm"
            />
            <jet-input
              type="text"
              id="searchByName"
              placeholder="Escribe el nombre del cliente"
              class="w-full"
              v-model="searchByName"
            />
          </div>

          <div class="flex flex-col">
            <custom-label
              for="searchByDocument"
              value="Buscar por Numero de Documento"
              class="uppercase mb-2 text-sm"
            />
            <jet-input
              type="text"
              id="searchByDocument"
              placeholder="Escribe el nombre del cliente"
              class="w-full"
              v-model="searchByDocument"
            />
          </div>
        </div>

        <!-- Tabla con los datos de los clientes -->
        <div
          class="
            h-[28rem]
            shadow
            border-b border-gray-300
            overflow-y-auto overflow-x-auto
          "
        >
          <table class="relative min-w-full table-auto">
            <thead class="sticky top-0 bg-gray-50">
              <tr>
                <!-- Customer Image -->
                <th
                  scope="col"
                  class="
                    px-6
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
                    px-6
                    py-3
                    text-left text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Nombres y Apellidos
                </th>

                <!-- Documento -->
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Documento
                </th>

                <!-- Tipo -->
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Tipo
                </th>

                <!-- Correo -->
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-gray-500
                    tracking-wider
                    uppercase
                  "
                >
                  Correo
                </th>

                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="(customer, index) in customerList"
                :key="customer.id"
                class="transition-colors hover:bg-indigo-50"
              >
                <!-- Imagen del cliente -->
                <td class="px-3 py-2 text-gray-800 text-center text-gray-800">
                  {{ index + 1 }}
                </td>

                <!-- Nombres -->
                <td class="px-3 py-2 text-gray-800">
                  <div class="flex">
                    <div class="w-16 h-16 p-2 mr-2">
                      <img
                        :src="customer.image_url"
                        :alt="customer.full_name"
                        class="w-full rounded-full"
                      />
                    </div>
                    <div class="flex flex-col justify-center">
                      <span>{{ customer.first_name }}</span>
                      <span>{{ customer.last_name }}</span>
                    </div>
                  </div>
                </td>

                <!-- Documento -->
                <td class="px-3 py-2 text-gray-800">
                  <span v-if="customer.document_number" class="tracking-widest">
                    {{ customer.document_number }}
                  </span>
                  <span v-else class="text-gray-400">No registrado.</span>
                </td>

                <!-- Documento -->
                <td class="px-3 py-2 text-gray-800">
                  <span v-if="customer.document_number">
                    {{ customer.document_type }}
                  </span>
                  <span v-else class="text-gray-400">No registrado.</span>
                </td>

                <!-- Correo Electronico -->
                <td class="px-3 py-2 text-gray-800">
                  <span
                    v-if="customer.email"
                    :class="{
                      'text-sm': customer.email.length > 30,
                    }"
                    >{{ customer.email }}</span
                  >
                  <span v-else class="text-gray-400">No registrado.</span>
                </td>

                <!-- Acciones -->
                <td class="px-3 py-2">
                  <div class="flex justify-end">
                    <row-button
                      type="show"
                      class="mr-2"
                      :href="route('customer.index')"
                      title="Ver Cliente"
                    />
                    <row-button
                      type="edit"
                      class="mr-2"
                      title="Editar Cliente"
                      :href="route('customer.edit', customer.id)"
                    />
                    <row-button
                      type="delete"
                      @click="deleteCustomer(customer)"
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
    customers: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      searchByName: null,
      searchByDocument: null,
    };
  },
  methods: {
    /**
     * Muestra el modal donde le solicita al usuario
     * que confirme la eliminación del cliente.
     */
    deleteCustomer(customer) {
      //Url de la petición
      let url = route("customer.destroy", customer.id);

      //Mensaje para la ventana modal.
      let message = `Esta acción es irreversible `;
      message += "y eliminará todos los datos del cliente ";
      message += `<b>${customer.full_name}</b> de la base de datos.`;

      //Ventana modal sweetalert
      Swal.fire({
        title: "¿Desea Eliminar este cliente?",
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
              title: `¡Cliente Eliminado!`,
              text: `El cliente ${res.customer.full_name} fue eliminado`,
            });

            this.removeCustomer(res.customer.id);
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
    removeCustomer(customerId) {
      //Se busca el index del cliente
      let index = this.customers.findIndex((c) => c.id === customerId);
      this.customers.splice(index, 1);
    },
    /**
     * Se encarga de mostrar el mensaje de error al
     * usuario de la plataforma.
     */
    async showError(error, customer) {
      let title = "¡Opps, algo salio mal!";
      let icon = "error";
      let message = null;

      if (error.status == 404) {
        //Se redacta el mensaje a mostrar
        message = "El cliente ";
        message += `<b>${customer.full_name}</b> no fue encontrado.`;
      } else {
        message = `EL cliente <b>${customer.full_name}</b> `;
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
     * @param String name Es el nombre que se va a filtrar
     * @param String customers Listado de clientes a filtrar.
     * @returns Array
     */
    filterByName(name, customers) {
      name = this.normalizeString(name);
      return customers.filter((c) => {
        let fullName = this.normalizeString(c.full_name);
        return fullName.includes(name);
      });
    },
    /**
     * Filtra los clientes por el numero de documento
     * @param String document Documento utilizado para filtrar
     * @param String customers Listado de clientes a filtrar.
     * @returns Array
     */
    filterByDocument(document, customers) {
      document = this.normalizeString(document);
      return customers.filter((c) => {
        if (c.document_number) {
          let documentNumber = this.normalizeString(c.document_number);
          return documentNumber.includes(document);
        }

        return false;
      });
    },
  },
  computed: {
    customerList() {
      //Se da prioridad al nombre por ser un campo obligatorio.
      let result = this.customers;
      let name = this.searchByName;
      let document = this.searchByDocument;

      if (name) {
        result = this.filterByName(name, result);
        if (document) {
          result = this.filterByDocument(document, result);
        }
      } else if (document) {
        result = this.filterByDocument(document, result);
      }

      return result;
    },
  },
};
</script>