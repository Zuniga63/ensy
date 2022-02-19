<template>
  <app-layout title="Cajas">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ cashbox.name }}
      </h2>

      <p class="text-sm lg:text-base text-gray-400" v-if="cashbox.code">
        codigo: {{ cashbox.code }}
      </p>
      <p class="lg:hidden text-base lg:text-lg text-gray-400">
        Saldo:
        <span class="font-bold"> {{ formatCurrency(cashbox.balance) }} </span>
      </p>
    </template>

    <div>
      <div
        class="relative w-full px-2 pt-5 pb-20"
        :class="{ 'blur-sm': modal }"
      >
        <!-- Tab Component -->
        <div
          class="
            relative
            border border-gray-200
            rounded-lg
            bg-white
            min-h-screen
          "
        >
          <div class="p-4">
            <!-- TABS & CONTROLS -->
            <div class="flex justify-between items-center pr-4">
              <!-- Tabs -->
              <ul class="flex flex-wrap list-none mb-2">
                <li
                  v-for="itemTab in tabs"
                  :key="itemTab"
                  @click.stop="tab = itemTab"
                >
                  <a
                    href="javascript:;"
                    class="
                      block
                      px-3
                      py-3
                      my-2
                      border-x-0 border-t-0 border-b-2
                      font-bold
                      text-xs
                      leading-tight
                      uppercase
                      hover:border-transparent hover:bg-gray-100
                      focus:border-blue-500
                    "
                    :class="{
                      'border-blue-500 text-blue-500 hover:border-blue-500':
                        tab === itemTab,
                    }"
                  >
                    {{ itemTab }}
                  </a>
                </li>
              </ul>

              <div class="hidden lg:flex">
                <!-- Selector de caja -->
                <select
                  name="box"
                  id="boxSelector"
                  v-model="boxSlug"
                  @change="chageBox"
                  class="
                    w-80
                    px-4
                    py-2
                    border border-gray-600
                    rounded
                    mr-3
                    text-sm
                    focus:ring focus:ring-indigo-400 focus:ring-opacity-50
                  "
                >
                  <option :value="null">Selecciona la caja a visitar</option>
                  <option v-for="box in boxs" :key="box.id" :value="box.slug">
                    {{ box.name }}
                  </option>
                </select>

                <!-- Boton para recargar -->
                <Link
                  :href="route('cashbox.show', cashbox.slug)"
                  preserve-state
                  preserve-scroll
                  class="
                    p-2
                    border border-gray-400
                    rounded
                    bg-gray-200
                    text-gray-700
                    hover:ring
                    hover:ring-gray-500
                    hover:ring-opacity-20
                    hover:bg-gray-50
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
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
            </div>

            <!-- Content -->
            <div class="pb-1">
              <ShowBoxInfo v-show="tab === tabs[0]" />
              <ShowTransactions
                v-show="tab === tabs[1]"
                :transactions="cashbox.transactions"
                @update-transaction="updateTransaction"
              />
              <ShowBoxClosures v-show="tab === tabs[2]" />
            </div>
          </div>
        </div>
      </div>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          class="
            fixed
            inset-0
            flex
            items-center
            justify-center
            bg-indigo-300 bg-opacity-30
            z-50
          "
          v-show="modal"
          @click.self="hiddenModal"
        >
          <transition
            name="show-form-modal"
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
          >
            <new-transaction-form
              @hidden-form="hiddenModal"
              :cashbox-id="cashbox.id"
              :max-date="maxDate"
              :transaction="transactionToUpdate"
              v-show="transactionFormActive"
            />
          </transition>

          <transition
            name="show-form-modal"
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
          >
            <transfer-form
              @hidden-form="hiddenModal"
              :cashbox-id="cashbox.id"
              :max-date="maxDate"
              :balance="cashbox.balance"
              :boxs="boxs"
              v-show="transferFormActive"
            />
          </transition>
        </div>
      </transition>

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
              p-4
              mb-2
              border border-emerald-700
              rounded-full
              bg-emerald-500
              shadow
              text-white
              outline-none
              hover:bg-opacity-50 hover:border-opacity-50
              focus:outline-transparent
              focus:outline-hidden
              focus:bg-emerald-600
            "
            v-show="tab === 'transacciones' && canTransferMoney"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 lg:h-5 lg:w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
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
              p-4
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
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 lg:h-5 lg:w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
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
// import Swal from "sweetalert2";

import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import ShowTransactions from "@/Pages/Cashbox/Components/ShowTransactions.vue";
import ShowBoxInfo from "@/Pages/Cashbox/Components/ShowBoxInfo.vue";
import ShowBoxClosures from "@/Pages/Cashbox/Components/ShowBoxClosures.vue";
import NewTransactionForm from "@/Pages/Cashbox/Components/TransactionForm.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";
import localizedFormat from "dayjs/plugin/localizedFormat";
import TransferForm from "./Components/TransferForm.vue";

export default {
  components: {
    AppLayout,
    JetButton,
    JetDangerButton,
    ShowTransactions,
    ShowBoxInfo,
    ShowBoxClosures,
    NewTransactionForm,
    Link,
    TransferForm,
  },
  props: ["cashbox", "boxs"],
  setup(props) {
    //---------------------------------------------------------
    // SE CONSTRUYE EL FORMATEADOR DE MONEDA
    //---------------------------------------------------------
    let fractionDigits = 0;
    let style = "currency";
    let currency = "COP";

    let formater = new Intl.NumberFormat("es-CO", {
      style,
      currency,
      minimumFractionDigits: fractionDigits,
    });

    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);
    dayjs.extend(localizedFormat);

    props.cashbox.balance = parseFloat(props.cashbox.balance);

    return { formater };
  },
  data() {
    return {
      tabs: ["info", "transacciones", "cierres"],
      tab: "transacciones", //info, transactions, closures
      modal: false,
      transactionToUpdate: null,
      transactionFormActive: false,
      transferFormActive: false,
      boxSlug: null,
    };
  },
  methods: {
    formatCurrency(number) {
      return this.formater.format(number);
    },
    showModal() {
      this.modal = true;
    },
    hiddenModal() {
      this.modal = false;
      if (this.transactionToUpdate) {
        this.transactionToUpdate = null;
      }

      this.transactionFormActive = false;
      this.transferFormActive = false;
    },
    showTransactionForm() {
      this.showModal();
      this.transactionFormActive = true;
    },
    updateTransaction(data) {
      this.transactionToUpdate = data;
      this.showTransactionForm();
    },
    showTransferForm() {
      this.showModal();
      this.transferFormActive = true;
    },
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
    chageBox() {
      if (this.boxSlug) {
        let url = route("cashbox.show", this.boxSlug);
        Inertia.get(url);
      }
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
      return dayjs().subtract(1, "day").format("YYYY-MM-DD");
    },
    canTransferMoney() {
      return this.cashbox.balance > 0 && this.boxs.length > 0;
    },
  },
};
</script>