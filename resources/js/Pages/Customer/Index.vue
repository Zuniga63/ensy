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
                <!-- # -->
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
                <!-- Imagen. nombre y documento -->
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

                <!-- Contacto -->
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
                  Contacto
                </th>

                <!-- Numero de cuenta -->
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
                  N° de Cuenta
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
                <!-- Index -->
                <td class="px-3 py-2 text-gray-800 text-center text-gray-800">
                  {{ index + 1 }}
                </td>

                <!-- Nombres y correo-->
                <td class="px-3 py-2 text-gray-800">
                  <div class="flex">
                    <!-- Customer Image -->
                    <div class="w-16 h-16 p-2 mr-2">
                      <img
                        :src="customer.image_url"
                        :alt="customer.full_name"
                        class="w-full rounded-full"
                      />
                    </div>
                    <!-- Nombre y correo -->
                    <div class="flex flex-col justify-center">
                      <span class="capitalize">{{ customer.full_name }}</span>
                      <!-- Documento -->
                      <span
                        v-if="customer.document_number"
                        class="tracking-widest text-sm"
                      >
                        <span class="text-gray-400"
                          >{{ customer.document_type }}:
                        </span>
                        <span @click="selectText">
                          {{ customer.document_number }}
                        </span>
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Contacto -->
                <td class="px-3 py-2 text-gray-800">
                  <div
                    class="flex flex-col"
                    v-if="customer.email || customer.contacts?.length"
                  >
                    <!-- Email -->
                    <div
                      v-if="customer.email"
                      class="flex items-center text-indigo-500"
                    >
                      <!-- Heroicon: Inbox -->
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                        />
                      </svg>

                      <span
                        class="text-sm hover:cursor-pointer"
                        @click="selectText"
                      >
                        {{ customer.email }}
                      </span>
                    </div>
                    <!-- Telefonos -->
                    <div class="flex" v-if="customer.contacts?.length">
                      <!-- Primer Telefono -->
                      <div class="flex">
                        <!-- iconos8: whatsapp -->
                        <svg
                          fill="currentColor"
                          stroke="currentColor"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          class="h-4 w-4 text-green-600"
                          v-if="customer.contacts[0].whatsapp"
                        >
                          <path
                            d="M19.077,4.928C17.191,3.041,14.683,2.001,12.011,2c-5.506,0-9.987,4.479-9.989,9.985c-0.001,1.76,0.459,3.478,1.333,4.992L2,22l5.233-1.237c1.459,0.796,3.101,1.215,4.773,1.216h0.004c5.505,0,9.986-4.48,9.989-9.985C22.001,9.325,20.963,6.816,19.077,4.928z M12.007,19.979c-1.333-0.001-2.653-0.337-3.816-0.972L7.518,18.64l-0.745,0.176l-1.968,0.465l0.481-1.784l0.216-0.802l-0.415-0.719c-0.698-1.208-1.066-2.588-1.065-3.991C4.024,7.583,7.607,4,12.01,4c2.136,0.001,4.143,0.833,5.652,2.341c1.509,1.51,2.339,3.517,2.337,5.651C19.997,16.396,16.413,19.979,12.007,19.979z"
                          />
                          <path
                            d="M16.898,15.554c-0.208,0.583-1.227,1.145-1.685,1.186c-0.458,0.042-0.887,0.207-2.995-0.624c-2.537-1-4.139-3.601-4.263-3.767c-0.125-0.167-1.019-1.353-1.019-2.581S7.581,7.936,7.81,7.687c0.229-0.25,0.499-0.312,0.666-0.312c0.166,0,0.333,0,0.478,0.006c0.178,0.007,0.375,0.016,0.562,0.431c0.222,0.494,0.707,1.728,0.769,1.853s0.104,0.271,0.021,0.437s-0.125,0.27-0.249,0.416c-0.125,0.146-0.262,0.325-0.374,0.437c-0.125,0.124-0.255,0.26-0.11,0.509c0.146,0.25,0.646,1.067,1.388,1.728c0.954,0.85,1.757,1.113,2.007,1.239c0.25,0.125,0.395,0.104,0.541-0.063c0.146-0.166,0.624-0.728,0.79-0.978s0.333-0.208,0.562-0.125s1.456,0.687,1.705,0.812c0.25,0.125,0.416,0.187,0.478,0.291C17.106,14.471,17.106,14.971,16.898,15.554z"
                          />
                        </svg>
                        <!-- Heroicon:phone -->
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          v-else
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                          />
                        </svg>
                        <!-- Numero -->
                        <span class="text-sm text-gray-800">
                          {{ customer.contacts[0].number }}
                        </span>
                      </div>

                      <span
                        class="text-sm mx-1"
                        v-if="customer.contacts.length > 1"
                        >-</span
                      >

                      <!-- Segundo Numero -->
                      <div class="flex" v-if="customer.contacts.length > 1">
                        <!-- iconos8: whatsapp -->
                        <svg
                          fill="currentColor"
                          stroke="currentColor"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          class="h-4 w-4 text-green-600"
                          v-if="customer.contacts[1].whatsapp"
                        >
                          <path
                            d="M19.077,4.928C17.191,3.041,14.683,2.001,12.011,2c-5.506,0-9.987,4.479-9.989,9.985c-0.001,1.76,0.459,3.478,1.333,4.992L2,22l5.233-1.237c1.459,0.796,3.101,1.215,4.773,1.216h0.004c5.505,0,9.986-4.48,9.989-9.985C22.001,9.325,20.963,6.816,19.077,4.928z M12.007,19.979c-1.333-0.001-2.653-0.337-3.816-0.972L7.518,18.64l-0.745,0.176l-1.968,0.465l0.481-1.784l0.216-0.802l-0.415-0.719c-0.698-1.208-1.066-2.588-1.065-3.991C4.024,7.583,7.607,4,12.01,4c2.136,0.001,4.143,0.833,5.652,2.341c1.509,1.51,2.339,3.517,2.337,5.651C19.997,16.396,16.413,19.979,12.007,19.979z"
                          />
                          <path
                            d="M16.898,15.554c-0.208,0.583-1.227,1.145-1.685,1.186c-0.458,0.042-0.887,0.207-2.995-0.624c-2.537-1-4.139-3.601-4.263-3.767c-0.125-0.167-1.019-1.353-1.019-2.581S7.581,7.936,7.81,7.687c0.229-0.25,0.499-0.312,0.666-0.312c0.166,0,0.333,0,0.478,0.006c0.178,0.007,0.375,0.016,0.562,0.431c0.222,0.494,0.707,1.728,0.769,1.853s0.104,0.271,0.021,0.437s-0.125,0.27-0.249,0.416c-0.125,0.146-0.262,0.325-0.374,0.437c-0.125,0.124-0.255,0.26-0.11,0.509c0.146,0.25,0.646,1.067,1.388,1.728c0.954,0.85,1.757,1.113,2.007,1.239c0.25,0.125,0.395,0.104,0.541-0.063c0.146-0.166,0.624-0.728,0.79-0.978s0.333-0.208,0.562-0.125s1.456,0.687,1.705,0.812c0.25,0.125,0.416,0.187,0.478,0.291C17.106,14.471,17.106,14.971,16.898,15.554z"
                          />
                        </svg>
                        <!-- Heroicon:phone -->
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          v-else
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                          />
                        </svg>
                        <!-- Numero -->
                        <span class="text-sm text-gray-800">
                          {{ customer.contacts[1].number }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <span v-else class="text-gray-400">No registrado.</span>
                </td>

                <!-- Numero de cuenta -->
                <td class="px-3 py-2 text-gray-800">
                  <div
                    v-if="
                      customer.information &&
                      customer.information.bank_account_number
                    "
                  >
                    <div class="flex flex-col items-center">
                      <!-- Banco y tipo de cuenta -->
                      <div class="text-gray-400 text-sm">
                        <span v-if="customer.information.bank_name">
                          {{ customer.information.bank_name }}
                        </span>
                        <span
                          v-if="
                            customer.information.bank_account_type == 'savings'
                          "
                        >
                          - Ahorros
                        </span>
                        <span
                          v-if="
                            customer.information.bank_account_type == 'current'
                          "
                        >
                          - Corriente
                        </span>
                      </div>
                      <!-- Numero de cuenta -->
                      <span>
                        {{ customer.information.bank_account_number }}
                      </span>
                    </div>
                  </div>
                  <p v-else class="text-gray-400 text-center">No registrado.</p>
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
    /**
     * Codigo que me permite seleccionar el texto de un elemento
     * tomado de: https://stackoverflow.com/questions/985272/selecting-text-in-an-element-akin-to-highlighting-with-your-mouse
     */
    selectText(event) {
      let node = event.target;
      if (document.body.createTextRange) {
        const range = document.body.createTextRange();
        range.moveToElementText(node);
        range.select();
      } else if (window.getSelection) {
        const selection = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(node);
        selection.removeAllRanges();
        selection.addRange(range);
      } else {
        console.warn("Could not select text in node: Unsupported browser.");
      }
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