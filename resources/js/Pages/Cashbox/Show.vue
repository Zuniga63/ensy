<template>
  <app-layout :title="cashbox.name">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ cashbox.name }}
      </h2>

      <p class="text-sm lg:text-base text-gray-400" v-if="cashbox.code">codigo: {{ cashbox.code }}</p>
      <p class="lg:hidden text-base lg:text-lg text-gray-400">
        Saldo:
        <span class="font-bold"> {{ formatCurrency(cashbox.balance) }} </span>
      </p>
    </template>

    <div class="relative grid grid-cols-5 gap-2 items-start w-full lg:w-11/12 mx-auto pt-5">
      <!-- Sidebar -->
      <aside class="hidden lg:block sticky top-4 w-full">
        <div class="px-2 py-4 border border-gray-200 rounded bg-white shadow-md">
          <h2 class="mb-4 text-gray-800 font-semibold text-center text-sm uppercase">Cajas Activas</h2>
          <div
            v-for="(box, index) in boxs"
            :key="index"
            class="p-2 mb-4 last:mb-0 rounded hover:cursor-pointer hover:opacity-80 transition-opacity"
            :class="{
              'bg-gray-800': box.id !== cashbox.id,
              'bg-indigo-600': box.id === cashbox.id,
            }"
            @click="chageBox(box.slug)"
          >
            <h2 class="mb-2 text-gray-50 text-sm text-center font-bold">{{ box.name }}</h2>
            <p class="text-xs text-gray-50 text-center font-bold">{{ formatCurrency(box.balance) }}</p>
          </div>
        </div>
      </aside>

      <!-- Contenido -->
      <div class="relative col-span-5 lg:col-span-4 w-full px-2 pb-20">
        <!-- Tab Component -->
        <tab-component :tabs="tabs" :tabSelected="tab" @selectTab="tab = $event">
          <template #controls>
            <div class="">
              <!-- Boton para recargar -->
              <Link
                :href="route('cashbox.show', cashbox.slug)"
                preserve-state
                preserve-scroll
                class="
                  block
                  p-1
                  sm:p-2
                  border border-gray-400
                  rounded-full
                  bg-gray-200
                  text-gray-700
                  hover:ring hover:ring-gray-500 hover:ring-opacity-20 hover:bg-gray-50
                  transition-colors
                "
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 lg:h-6 lg:w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
              </Link>
            </div>
          </template>

          <template #transacciones>
            <ShowTransactions
              :transactions="cashbox.transactions"
              @update-transaction="updateTransaction"
              @delete-transaction="deleteTransaction"
            />
          </template>

          <template #info>
            <ShowBoxInfo :transactions="cashbox.transactions" />
          </template>

          <template #cierres>
            <ShowBoxClosures />
          </template>
        </tab-component>
      </div>

      <modal-component :show="modal" :closeable="closeableModal" @close="hiddenModal" maxWidth="sm">
        <new-transaction-form
          :cashbox-id="cashbox.id"
          :max-date="maxDate"
          :transaction="transactionToUpdate"
          :lastDate="lastDate"
          :lastTime="lastTime"
          @lockModal="closeableModal = false"
          @unlockModal="closeableModal = true"
          @updateTime="updateTime"
          @hidden-form="hiddenModal"
          v-show="transactionFormActive"
        />

        <transfer-form
          :cashbox-id="cashbox.id"
          :max-date="maxDate"
          :balance="cashbox.balance"
          :boxs="boxs"
          :lastDate="lastDate"
          :lastTime="lastTime"
          @lockModal="closeableModal = false"
          @unlockModal="closeableModal = true"
          @updateTime="updateTime"
          @hidden-form="hiddenModal"
          v-show="transferFormActive"
        />
      </modal-component>
      <!-- Button for show modal  -->
      <div class="fixed bottom-4 right-4" v-show="!modal">
        <div class="flex flex-col">
          <!-- Boton para transferencia -->
          <a
            href="javascript:;"
            @click="showTransferForm"
            class="
              flex
              items-center
              justify-center
              w-full
              h-full
              p-3
              lg:p-4
              mb-2
              border border-emerald-700
              rounded-full
              bg-emerald-500
              shadow
              text-white
              outline-none
              hover:bg-opacity-50 hover:border-opacity-50
              focus:outline-transparent focus:outline-hidden focus:bg-emerald-600
            "
            v-show="tab === 'transacciones' && canTransferMoney"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path
                d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"
              />
            </svg>
          </a>
          <!-- Button for new transaction -->
          <a
            href="javascript:;"
            @click="showTransactionForm"
            class="
              flex
              items-center
              justify-center
              w-full
              h-full
              p-3
              lg:p-4
              border border-blue-700
              rounded-full
              bg-blue-600
              shadow-md
              outline-none
              text-white
              hover:bg-opacity-50 hover:border-opacity-50
              focus:outline-transparent focus:outline-hidden focus:bg-blue-800
            "
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </app-layout>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";

import AppLayout from "@/Layouts/AppLayout.vue";
import ShowTransactions from "@/Pages/Cashbox/Components/ShowTransactions.vue";
import ShowBoxInfo from "@/Pages/Cashbox/Components/ShowBoxInfo.vue";
import ShowBoxClosures from "@/Pages/Cashbox/Components/ShowBoxClosures.vue";
import NewTransactionForm from "@/Pages/Cashbox/Components/TransactionForm.vue";
import TransferForm from "./Components/TransferForm.vue";
import TabComponent from "@/Components/Tab.vue";
import ModalComponent from "@/Components/Modal.vue";

import Swal from "sweetalert2";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/es-do";
import localizedFormat from "dayjs/plugin/localizedFormat";

import { formatCurrency } from "@/utilities";

export default {
  components: {
    Link,
    AppLayout,
    ShowTransactions,
    ShowBoxInfo,
    ShowBoxClosures,
    NewTransactionForm,
    TransferForm,
    TabComponent,
    ModalComponent,
  },
  props: ["cashbox", "boxs"],
  setup(props) {
    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale("es-do");
    dayjs.extend(relativeTime);
    dayjs.extend(localizedFormat);

    props.cashbox.balance = parseFloat(props.cashbox.balance);
  },
  data() {
    return {
      tabs: ["transacciones", "info", "cierres"],
      tab: "transacciones", //info, transactions, closures
      modal: false,
      closeableModal: true,
      lastDate: null,
      lastTime: null,
      transactionToUpdate: null,
      transactionFormActive: false,
      transferFormActive: false,
      boxSlug: null,
    };
  },
  methods: {
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    //              METODOS PARA ELIMINAR TRANSACCIÓNES
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    /**
     * Se encarga de preguntar en primer lugar al usuario si desea eliminar
     * la transación y en segunda instancia realizar la petición y remover
     * dicha transacción.
     * @param {onject} transaction instancia de la transacción a eliminar.
     */
    deleteTransaction(transaction) {
      let urlName = "cashbox.destroyTransaction";
      let urlParam = [transaction.cashbox_id, transaction.id];
      let url = route(urlName, urlParam);

      //Se construye el mensaje al usuario
      let title = "¿Está seguro que desea eliminar esta transacción?";
      let message = "Una vez eliminado de la base de datos no puede revertirse.";

      Swal.fire({
        title,
        icon: "warning",
        text: message,
        showCancelButton: true,
        confirmButtonText: "¡Si, eliminalo!",
        cancelButtonText: "Cancelar",
        showLoaderOnConfirm: true,
        backdrop: true,
        preConfirm: async () => {
          try {
            const res = await window.axios.delete(url);
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
      }).then(async (result) => {
        //Los datos provenientes del servidor.
        let res = result.value;

        if (result.isConfirmed) {
          if (res.ok) {
            Swal.fire({
              title: `¡Transacción Eliminada!`,
              icon: "success",
              text: `La transacción con ID:${res.transaction.id} fue eliminada.`,
            });

            this.removeTransaction(res.transaction);
          } else {
            this.showError(res, transaction);
            //Se refrescan los datos de la caja
            this.$inertia.reload({
              only: ["cashbox"],
            });
          }
        }
      });
    },
    /**
     * Se encarga de remover la transacción del listado de transacciones
     * @param {object} transaction identificador de la transacción.
     */
    removeTransaction(transaction) {
      //Se busca el index de la transaccion
      let index = this.cashbox.transactions.findIndex((t) => t.id === transaction.id);
      if (index >= 0) {
        this.cashbox.transactions.splice(index, 1);
        this.calculateBalance();
      }
    },
    /**
     * Habilita el cambio de la caja que se está mostrando
     * @param {string} boxSlug - identificacion de la caja a visitar.
     */
    chageBox(boxSlug) {
      if (boxSlug) {
        let url = route("cashbox.show", boxSlug);
        Inertia.get(url);
      }
    },
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    //      CONTROL DE MODALES Y FORMULARIOS
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    /**
     * Permite que el backdrop del modal se visualice.
     */
    showModal() {
      this.modal = true;
    },
    /**
     * Oculta tanto el modal como el backdrop del modal
     * y si es una actualización tambien resetea su valor.
     */
    hiddenModal() {
      this.modal = false;
      if (this.transactionToUpdate) {
        this.transactionToUpdate = null;
      }

      this.transactionFormActive = false;
      this.transferFormActive = false;
    },
    /**
     * Activa el formulario para registrar una nueva transacción
     */
    showTransactionForm() {
      this.showModal();
      this.transactionFormActive = true;
    },
    /**
     * Activa el formulario para actualizar una transacción
     * @param {object} data Es la instancia de la transacción a modificar.
     */
    updateTransaction(data) {
      this.transactionToUpdate = data;
      this.showTransactionForm();
    },
    updateTime(time) {
      this.lastDate = time.date;
      this.lastTime = time.time;
    },
    /**
     * Activa el formulario para registrar una transferencia.
     */
    showTransferForm() {
      this.showModal();
      this.transferFormActive = true;
    },
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    //                  UTILIDADES Y METODOS DE RENDERIZADO
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    /**
     * Se encarga de dar formato de moneda al numero pasado como parametro
     * @param {number} number Numero a formatear
     * @return {string} Numero formateado
     */
    formatCurrency,
    /**
     * Se encarga de agregar los propiedades necesarias
     * a las transacciones y transformar las fechas a
     * instancias de Dayjs
     */
    transformTransactions() {
      this.cashbox.transactions.map((item) => {
        item.amount = parseFloat(item.amount);
        item.balance = null;
        this.createDayjsInstances(item);

        return item;
      });

      this.calculateBalance();
    },
    /**
     * Se encarga de establece el saldo en el
     * momento de latransación.
     */
    calculateBalance() {
      let balance = 0;
      this.cashbox.transactions.forEach((item) => {
        balance += item.amount;
        item.balance = balance;
      });
    },
    /**
     * Crea las instancias dayjs a las propiedades
     * relacionadas a la fecha.
     */
    createDayjsInstances(trans) {
      trans.date = dayjs(trans.date);
      trans.createdAt = dayjs(trans.createdAt);
      trans.updatedAt = dayjs(trans.updatedAt);
    },
    /**
     * Se encarga de mostrar el mensaje de error al
     * usuario de la plataforma.
     */
    async showError(error, transaction) {
      let title = "¡Opps, algo salio mal!";
      let icon = "error";
      let message = null;

      if (error.status == 404) {
        message = "La transacción no existe o fue eliminada.";
      } else {
        message = error.message ? error.message : "No se pudo completar la petición. Contacte con el administrador.";
      }

      Swal.fire({
        title,
        icon,
        html: message,
      });
    },
  },
  beforeMount() {
    this.transformTransactions();
  },
  beforeUpdate() {
    this.transformTransactions();
  },
  mounted() {
    // console.log(this.cashbox);
  },
  computed: {
    currentTabComponent() {
      if (this.tab === "info") {
        return "ShowBoxInfo";
      } else if (this.tab === "transacciones") {
        return "ShowTransactions";
      } else if (this.tab === "cierres") {
        return "ShowBoxClosures";
      } else {
        return "ShowBoxInfo";
      }
    },
    maxDate() {
      return dayjs().format("YYYY-MM-DD");
    },
    canTransferMoney() {
      return this.cashbox.balance > 0 && this.boxs.length > 0;
    },
  },
};
</script>