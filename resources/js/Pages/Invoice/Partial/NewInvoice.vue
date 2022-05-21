<template>
  <div
    class="
      w-full
      border-0 border-gray-800 border-opacity-80
      bg-white
      overflow-hidden
    "
  >
    <invoice-header :config="config" />

    <div class="overflow-hidden">
      <div
        class="flex flex-nowrap transition-transform duration-300"
        :class="{ '-translate-x-full': showingPaymentForm }"
      >
        <!-- Interfaz para agregar los detalles -->
        <div class="w-full flex-shrink-0 px-6 py-4">
          <!-- Customer Information and Invoice Date -->
          <div class="grid grid-cols-4 gap-x-4 mb-6">
            <!-- Customer Information-->
            <div class="col-span-3">
              <customer-information
                :customers="customers"
                :customer="customer"
                :errors="errors"
              />
            </div>

            <!-- Invoice Date -->
            <div class="col-span-1">
              <invoice-dates
                v-model:expedition-date="expeditionDate"
                v-model:expiration-date="expirationDate"
                :errors="errors"
              />
            </div>
          </div>

          <!-- Interfaz for new Item -->
          <new-item class="mb-4" @add-item="addItem" />

          <!-- Listado de items y sumary -->
          <div class="grid grid-cols-4 gap-2 mb-3">
            <!-- Listado de Items -->
            <div class="col-span-3">
              <item-list
                :item-list="itemList"
                @remove-item="removeItem"
                :errors="errors"
              />
              <input-error
                :message="errors.invoiceItems"
                class="mt-1 text-xs"
              />
            </div>

            <div class="">
              <invoice-sumary :sumary="sumary" only-invoice />
            </div>
          </div>
        </div>

        <!-- Interfaz para agregar las formas de pago -->
        <div class="w-full flex-shrink-0 px-6 py-4" v-show="showingPaymentForm">
          <div class="grid grid-cols-4 gap-4 items-start">
            <!-- Cash Controls -->
            <div class="col-span-3">
              <cash-payment :boxs="boxs" :sumary="sumary" @add-payment="addPayment"  />

              <!-- Payment boxs -->
              <payment-list
                :payments="payments"
                @remove-payment="removePayment"
                @update-order="sortPayemntList"
              />
            </div>

            <!-- Sumary of Payments -->
            <invoice-sumary :sumary="sumary" />

            <div class="col-span-4 px-4">
              <!-- Notes -->
              <div v-show="generalErrors.length == 0">
                <p class="mb-2 text-xs text-gray-500">
                  <span class="font-bold">Nota 1:</span> Cuando hay que dar
                  vueltos al cliente se utilizan las cajas marcadas para tal
                  fin, en la cuales la transacción será modificada. En el caso
                  de no marcar ninguna, las cajas empleadas son las últimas del
                  listado.
                </p>

                <p class="text-xs text-gray-500">
                  <span class="font-bold">Nota 2:</span> Las facturas con
                  créditos solo se harán efectivas con clientes registrados en
                  la plataforma.
                </p>
              </div>

              <!-- General Errors -->
              <ul v-show="generalErrors.length > 0" class="list-disc">
                <li
                  v-for="(generalError, index) in generalErrors"
                  :key="index"
                  class="text-sm text-red-600"
                >
                  {{ generalError }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer class="flex justify-end px-6 py-3 bg-gray-200">
      <jet-button @click="formToggler" :disabled="!enabledPaymentForm">
        <span v-if="!showingPaymentForm">Registrar Forma de Pago</span>
        <span v-else>Regresar</span>
      </jet-button>

      <JetButton
        v-show="showingPaymentForm"
        class="ml-2"
        @click="storeInvoice"
        :disabled="!enabledInvoice"
      >
        <!-- Spin -->
        <svg
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          v-show="processing"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>

        <span v-show="processing">Procesando</span>
        <span v-show="!processing">Registrar Factura</span>
      </JetButton>
    </footer>
  </div>
</template>
<script>
import InvoiceHeader from "./NewInvoice/InvoiceHeader.vue";
import CustomerInformation from "./NewInvoice/CustomerInformation.vue";
import InvoiceDates from "./NewInvoice/InvoiceDates.vue";
import NewItem from "./NewInvoice/InvoiceNewItem.vue";
import ItemList from "./NewInvoice/ItemList.vue";
import InvoiceSumary from "./NewInvoice/InvoiceSumary.vue";
import CashPayment from "./CashPayment.vue";
import PaymentList from "./NewInvoice/PaymentList.vue";
import JetButton from "@/Jetstream/Button.vue";
import InputError from "@/Components/Form/InputError.vue";
import axios from "axios";
import dayjs from "dayjs";

export default {
  components: {
    InvoiceHeader,
    CustomerInformation,
    InvoiceDates,
    JetButton,
    NewItem,
    ItemList,
    InvoiceSumary,
    CashPayment,
    PaymentList,
    InputError,
  },
  props: {
    customers: {
      type: Array,
      default: [],
    },
    boxs: {
      type: Array,
      default: [],
    },
    config: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      showingPaymentForm: false,
      /**
       * Información del cliente.
       */
      customer: {
        fullName: null,
        nit: null,
        address: null,
        phone: null,
        customer: null,
      },
      expeditionDate: dayjs().format('YYYY-MM-DD'),
      expirationDate: dayjs().add(1, 'month').format('YYYY-MM-DD'),
      itemList: [],
      payments: [],
      errors: new Object(),
      generalErrors: [],
      processing: false,
    };
  },
  methods: {
    formToggler() {
      if (this.enabledPaymentForm) {
        this.showingPaymentForm = !this.showingPaymentForm;
      }
    },
    addItem(item) {
      this.itemList.push(item);
    },
    removeItem(index) {
      this.itemList.splice(index, 1);
    },
    validateGeneralForm() {
      return (
        this.customer.fullName &&
        this.customer.nit &&
        this.expeditionDate &&
        this.expirationDate &&
        this.sumary.total > 0
      );
    },
    addPayment(payment) {
      //Se busca si la caja ya está agregada
      if (this.payments.some((item) => item.box.id === payment.box.id)) {
        let index = this.payments.findIndex(
          (item) => item.box.id === payment.box.id
        );
        this.payments[index].amount += payment.amount;
      } else {
        this.payments.push(payment);
      }

      this.sortPayemntList();
    },
    /**
     * Se encarga de poner al final de la lista los pagos
     * que están marcados para cambios.
     */
    sortPayemntList() {
      //console.log("handler");
      //Primero se ordenan de mayor a menor
      this.payments.sort((p1, p2) => {
        if (p1.amount > p2.amount) return -1;

        if (p1.amount < p2.amount) return 1;

        return 0;
      });

      //Despues se ponen al final los que están para pagos
      this.payments.sort((p1, p2) => {
        if (p1.useForChange && !p2.useForChange) return 1;
        if (!p1.useForChange && p2.useForChange) return -1;
        if (p1.useForChange && p2.useForChange) {
          if (p1.amount > p2.amount) return -1;

          if (p1.amount < p2.amount) return 1;

          return 0;
        }

        return 0;
      });
      //console.log(this.payments);
    },
    removePayment(index) {
      this.payments.splice(index, 1);
    },
    /**
     * Realiza la petición al servidor para guardar
     * la factura creada.
     */
    async storeInvoice() {
      let url = route("invoice.store");
      let data = this.getData();
      this.processing = true;

      try {
        const res = await axios.post(url, data);
        if (res.data.ok) {
          this.$emit("addInvoice", res.data);
        } else {
          this.generalErrors = [];
          this.generalErrors.push(res.data.message);
        }
        console.log("Se obtuvo una respuesta del servidor");
        console.log(res.data);
      } catch (error) {
        if (error.response) {
          if (error.response.data?.errors) {
            this.createErrors(error.response.data?.errors);
          }
        } else if (error.request) {
          // La petición fue hecha pero no se recibió respuesta
          // `error.request` es una instancia de XMLHttpRequest en el navegador y una instancia de
          // http.ClientRequest en node.js
          console.log(error.request);
        } else {
          // Algo paso al preparar la petición que lanzo un Error
          console.log("Error", error.message);
        }
      } finally {
        this.processing = false;
      }
    },
    /**
     * Crea el objeto con la información que será enviada al servidor
     * @return {object}
     */
    getData() {
      let data = {};

      //Se establece la infomarción del cliente
      data.customerId = this.customer.customer
        ? this.customer.customer.id
        : null;
      data.customerName = this.customer.fullName;
      data.customerDocument = this.customer.nit;
      data.customerAddress = this.customer.address;
      data.customerPhone = this.customer.phone;

      data.expeditionDate = this.expeditionDate;
      data.expirationDate = this.expirationDate;

      //Se agregan los items
      data.invoiceItems = this.getItems();

      //Se agregan los pagos realizados
      data.invoicePayments = this.getPayments();

      //Finalmente se agrega el sumanry
      /* data.subtotal = this.sumary.subtotal;
      data.discount = this.sumary.discount;
      data.amount = this.sumary.total;
      data.cash = this.sumary.cash;
      data.credit = this.sumary.credit; */

      return data;
    },
    /**
     * Crea un arreglo de objetos con solo la información que el servidor
     * necesita para procesar la factura.
     */
    getItems() {
      let result = [];

      this.itemList.forEach((item) => {
        result.push({
          quantity: item.quantity,
          description: item.description,
          unitValue: item.unitValue,
          discount: item.discount,
          amount: item.amount,
        });
      });

      return result;
    },
    /**
     * Crea un arreglo de objetos con la información de los pagos en
     * efectivo del cliente que el servidor necesita.
     */
    getPayments() {
      let result = [];

      this.payments.forEach((payment) => {
        result.push({
          boxId: payment.box.id,
          boxName: payment.box.name,
          registerTransaction: payment.registerTransaction,
          useForChange: payment.useForChange,
          amount: payment.amount,
        });
      });

      return result;
    },
    createErrors(errors) {
      this.errors = new Object();

      for (const key in errors) {
        if (Object.hasOwnProperty.call(errors, key)) {
          const error = errors[key];

          //El servidor retorna los errores de los items con el formato invoiceItems.index.property
          if (key.includes("invoiceItems.")) {
            let data = key.split(".");
            //Recupero el item utilizando el index (segundo parametro del split)
            let item = this.itemList[parseInt(data[1])];
            //Recupero la propiedad que tiene el error
            let property = data.length > 2 ? data[2] : null;

            //Se actualiza el error en el item
            item.error = true;
            property ? item.addError(property) : null;

            console.log(data);
          } else {
            this.errors[key] = error[0];
          }
        }
      }

      console.log(this.errors);
    },
  }, //.end methods
  computed: {
    sumary() {
      let sumary = {
        subtotal: 0,
        discount: 0,
        total: 0,
        cash: 0,
        credit: 0,
        change: 0,
      };

      this.itemList.forEach((item) => {
        sumary.subtotal += _.round(item.quantity * item.unitValue);
        sumary.discount += _.round(item.quantity * item.discount);
      });

      sumary.total = sumary.subtotal - sumary.discount;

      //Se resumen los pagos
      this.payments.forEach((payment) => {
        sumary.cash += payment.amount;
      });

      if (sumary.total > sumary.cash) {
        sumary.credit = sumary.total - sumary.cash;
      } else {
        sumary.change = sumary.cash - sumary.total;
      }

      return sumary;
    },
    enabledPaymentForm() {
      return (
        this.expeditionDate && this.expirationDate && this.sumary.total > 0
      );
    },
    enabledInvoice() {
      //Se verifica si la primera parte está correcta
      if (this.enabledPaymentForm) {
        //Se verifica si se una factura a credito y so lo es
        if (
          this.sumary.credit <= 0 ||
          (this.sumary.credit > 0 && this.customer.customer)
        ) {
          return true;
        }
      }

      return false;
    },
  }, //.end computed
  beforeMount() {
    /* this.customer.fullName = "Andrés Felipe";
    this.customer.nit = "1064113927";
    this.expeditionDate = "2022-03-25";
    this.expirationDate = "2022-04-24";
    this.itemList.push({
      description: "Item generico",
      quantity: 3,
      unitValue: 500000,
      discount: 10000,
      amount: 1500000 - 30000,
      discountPercentage: 1,
    }); */
  },
};
</script>
