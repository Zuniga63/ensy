<template>
  <app-layout title="Clientes">
    <template #header>
      <div class="flex justify-between w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administración de Clientes</h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('customer.create')"> Registrar Cliente </link-button>
      </div>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="py-5 px-4 rounded-md bg-white shadow">
        <!-- Controles de busqueda -->
        <div class="grid grid-cols-4 gap-4 w-full mb-4">
          <!-- Busqueda por nombre -->
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <custom-label
              for="customerName"
              value="Nombre del Cliente"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
            />
            <jet-input
              type="text"
              id="customerName"
              placeholder="Ej: Juanito Perez"
              class="w-full text-sm"
              v-model="customerName"
            />
          </div>

          <!-- Busqueda por documento -->
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <custom-label
              for="document"
              value="Documento"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
            />
            <jet-input
              type="text"
              id="document"
              placeholder="Escribe el # de documento."
              class="w-full text-sm"
              v-model="document"
            />
          </div>

          <!-- Buscar por email -->
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <custom-label
              for="searchByEmail"
              value="Email"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
            />
            <jet-input
              type="text"
              id="searchByEmail"
              placeholder="ejemplo@ejemplo.com"
              class="w-full text-sm"
              v-model="email"
            />
          </div>

          <!-- Buscar por Bank Account -->
          <div class="relative pt-4 px-2 pb-2 border border-gray-400 rounded-md">
            <custom-label
              for="searchByBankAccount"
              value="Numero de cuenta"
              class="absolute top-0 left-4 p-1 bg-white text-sm text-gray-400 transform -translate-y-1/2"
            />
            <jet-input
              type="text"
              id="searchByBankAccount"
              placeholder="Escribe el numero de cuenta."
              class="w-full text-sm"
              v-model="bankAccount"
            />
          </div>
        </div>

        <!-- Tabla con los datos de los clientes -->
        <div class="h-[28rem] shadow border-b border-gray-300 overflow-y-auto overflow-x-auto">
          <table class="relative min-w-full table-auto">
            <thead class="sticky top-0 bg-gray-50">
              <tr>
                <!-- # -->
                <th scope="col" class="px-6 py-3 text-center text-gray-500 tracking-wider uppercase">#</th>
                <!-- Imagen. nombre y documento -->
                <th scope="col" class="px-6 py-3 text-left text-gray-500 tracking-wider uppercase">
                  Nombres y Apellidos
                </th>

                <!-- Contacto -->
                <th scope="col" class="px-6 py-3 text-left text-gray-500 tracking-wider uppercase">Contacto</th>

                <!-- Numero de cuenta -->
                <th scope="col" class="px-6 py-3 text-center text-gray-500 tracking-wider uppercase">N° de Cuenta</th>

                <!-- Saldo -->
                <th scope="col" class="px-6 py-3 text-center text-gray-500 tracking-wider uppercase">Saldo</th>

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
                      <img :src="customer.image_url" :alt="customer.full_name" class="w-full rounded-full" />
                    </div>
                    <!-- Nombre y correo -->
                    <div class="flex flex-col justify-center">
                      <span class="capitalize">{{ customer.full_name }}</span>
                      <!-- Documento -->
                      <span v-if="customer.document_number" class="tracking-widest text-sm">
                        <span class="text-gray-400">{{ customer.document_type }}: </span>
                        <span @click="selectText">
                          {{ formatDocument(customer.document_number) }}
                        </span>
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Contacto -->
                <td class="px-3 py-2 text-gray-800">
                  <div class="flex flex-col" v-if="customer.email || customer.contacts?.length">
                    <!-- Email -->
                    <div v-if="customer.email" class="flex items-center text-indigo-500">
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

                      <span class="text-sm hover:cursor-pointer" @click="selectText">
                        {{ customer.email }}
                      </span>
                    </div>
                    <!-- Telefonos -->
                    <div class="flex" v-if="customer.contacts?.length">
                      <!-- Primer Telefono -->
                      <div class="flex">
                        <!-- iconos8: whatsapp -->
                        <whatsapp-icon class="h-4 w-4 text-green-600" v-if="customer.contacts[0].whatsapp" />
                        <!-- Heroicon:phone -->
                        <phone-icon class="h-4 w-4" v-else />

                        <!-- Numero -->
                        <span class="text-sm text-gray-800">
                          {{ formatPhone(customer.contacts[0].number) }}
                        </span>
                      </div>

                      <span class="text-sm mx-1" v-if="customer.contacts.length > 1">-</span>

                      <!-- Segundo Numero -->
                      <div class="flex" v-if="customer.contacts.length > 1">
                        <!-- iconos8: whatsapp -->
                        <whatsapp-icon class="h-4 w-4 text-green-600" v-if="customer.contacts[1].whatsapp" />
                        <!-- Heroicon:phone -->
                        <phone-icon class="h-4 w-4" v-else />
                        <!-- Numero -->
                        <span class="text-sm text-gray-800">
                          {{ formatPhone(customer.contacts[1].number) }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <span v-else class="text-gray-400">No registrado.</span>
                </td>

                <!-- Numero de cuenta -->
                <td class="px-3 py-2 text-gray-800">
                  <div v-if="customer.information && customer.information.bank_account_number">
                    <div class="flex flex-col items-center">
                      <!-- Banco y tipo de cuenta -->
                      <div class="text-gray-400 text-sm">
                        <span v-if="customer.information.bank_name">
                          {{ customer.information.bank_name }}
                        </span>
                        <span v-if="customer.information.bank_account_type == 'savings'"> - Ahorros </span>
                        <span v-if="customer.information.bank_account_type == 'current'"> - Corriente </span>
                      </div>
                      <!-- Numero de cuenta -->
                      <span>
                        {{ customer.information.bank_account_number }}
                      </span>
                    </div>
                  </div>
                  <p v-else class="text-gray-400 text-center">No registrado.</p>
                </td>

                <td class="px-3 py-2 text-gray-800 text-right">
                  {{ formatCurrency(customer.balance) }}
                </td>

                <!-- Acciones -->
                <td class="px-3 py-2">
                  <div class="flex justify-end">
                    <row-button type="show" class="mr-2" :href="route('customer.index')" title="Ver Cliente" />
                    <row-button
                      type="edit"
                      class="mr-2"
                      title="Editar Cliente"
                      :href="route('customer.edit', customer.id)"
                    />
                    <row-button type="delete" @click="deleteCustomer(customer)" title="Eliminar Cliente" />
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
import WhatsappIcon from "@/Components/Svgs/Whatsapp.vue";
import PhoneIcon from "@/Components/Svgs/Phone.vue";

import Swal from "sweetalert2";
/* import axios from "axios"; */
import { formatCurrency, formatDocument, formatPhone, normalizeString, selectText } from "@/utilities";

export default {
  components: {
    AppLayout,
    JetButton,
    LinkButton,
    RowButton,
    CustomLabel,
    JetInput,
    WhatsappIcon,
    PhoneIcon,
  },
  props: {
    customers: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      /**
       * @type {Null|String} Nombre del cliente a buscar en el listado de clientes
       */
      customerName: null,
      /**
       * @type {String} Numero de documento del a buscar en el listado
       */
      document: null,
      /**
       * @type {string} Numero de cuenta del cliente a buscar
       */
      bankAccount: null,
      /**
       * @type {string} Correo electronico del cliente filtrar
       */
      email: null,
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
     * Filtra los clientes por su nombre
     * @param String name Es el nombre que se va a filtrar
     * @param String customers Listado de clientes a filtrar.
     * @returns Array
     */
    filterByName(name, customers) {
      name = normalizeString(name);
      return customers.filter((c) => {
        let fullName = normalizeString(c.full_name);
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
      document = normalizeString(document);
      return customers.filter((c) => {
        if (c.document_number) {
          let documentNumber = normalizeString(c.document_number);
          return documentNumber.includes(document);
        }

        return false;
      });
    },
    /**
     * Filtra los clientes por el correo electronico
     * @param {String} email Correo electronico.
     * @param {array} customers Listado de clientes a filtrar.
     * @returns {Array}
     */
    filterByEmail(email, customers) {
      email = normalizeString(email);
      return customers.filter((c) => {
        if (c.email) {
          let customerEmail = normalizeString(c.email);
          return customerEmail.includes(email);
        }
        return false;
      });
    },
    /**
     * Filtra los clientes por numero de cuenta.
     * @param {String} bankAccount Numero de cuenta
     * @param {array} customers Listado de clientes a filtrar.
     * @returns {Array}
     */
    filterByBankAccount(bankAccount, customers) {
      bankAccount = normalizeString(bankAccount);
      return customers.filter((customer) => {
        if (customer.information && customer.information.bank_account_number) {
          let account = normalizeString(customer.information.bank_account_number);
          return account.includes(bankAccount);
        }
        return false;
      });
    },
    selectText,
    formatDocument,
    formatPhone,
    formatCurrency,
  },
  computed: {
    customerList() {
      //Se da prioridad al nombre por ser un campo obligatorio.
      let result = this.customers;

      if (this.customerName) result = this.filterByName(this.customerName, result);
      if (this.document) result = this.filterByDocument(this.document, result);
      if (this.email) result = this.filterByEmail(this.email, result);
      if (this.bankAccount) result = this.filterByBankAccount(this.bankAccount, result);

      return result;
    },
  },
  /*  beforeMount() {
    console.log(this.customers);
  }, */
};
</script>