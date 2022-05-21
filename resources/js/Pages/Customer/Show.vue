<template>
  <app-layout :title="customer.full_name">
    <template #header>
      <div class="flex justify-between items-center w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ customer.full_name }}</h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('customer.index')" class=""> Regresar </link-button>
      </div>
    </template>

    <div class="w-full lg:w-11/12 py-10 sm:px-6 lg:px-8 mx-auto">
      <div class="relative grid grid-cols-3 gap-4 items-start">
        <!-- Sidebar -->
        <customer-info
          :customer="customer"
          class="static lg:sticky top-4 col-span-3 lg:col-span-1"
          @addNewPayment="showingModal = true"
        />
        <!-- Content -->
        <div class="col-span-3 lg:col-span-2 w-full">
          <tab-component :tabs="tabs" :tabSelected="tab" @selectTab="tab = $event">
            <template #controls>
              <!-- Control para las facturas -->
              <select
                name="invoiceFilter"
                v-show="tab === 'facturas'"
                v-model="invoiceFilter"
                class="border border-gray-200 rounded-sm text-gray-800 text-sm font-semibold"
              >
                <option value="all">Todas las facturas</option>
                <option value="pending">Solo pendientes</option>
                <option value="paid">Solo Pagadas</option>
                <option value="cancel">Solo Canceladas</option>
              </select>

              <select
                name=""
                v-show="tab === 'pagos'"
                v-model="paymentGroup"
                class="border border-gray-200 rounded-sm text-gray-800 text-sm font-semibold"
              >
                <option value="individual">Mostrar Individualmente</option>
                <option value="daily">Agrupar por días</option>
                <option value="monthly">Agrupar por mes</option>
              </select>
            </template>

            <template #facturas>
              <customer-invoices :invoices="customer.invoices" :filterBy="invoiceFilter" />
            </template>

            <template #pagos>
              <customer-payments :payments="customer.invoice_payments" :groupBy="paymentGroup" />
            </template>
          </tab-component>
        </div>
      </div>

      <!-- Modal -->
      <modal-component :show="showingModal" :closeable="!lockModal" maxWidth="sm" @close="closeModal">
        <customer-payment-form
          :customer="customer"
          :boxs="boxs"
          @hiddenForm="closeModal"
          @lockModal="lockModal = true"
          @unlockModal="lockModal = false"
        />
      </modal-component>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import CustomerInfo from "./Partial/CustomerInfo.vue";
import TabComponent from "@/Components/Tab.vue";
import CustomerInvoices from "./Partial/CustomerInvoices.vue";
import CustomerPayments from "./Partial/CustomerPayments.vue";
import ModalComponent from "@/Components/Modal.vue";
import CustomerPaymentForm from "./Partial/CustomerPaymentForm.vue";

export default {
  components: {
    AppLayout,
    LinkButton,
    CustomerInfo,
    TabComponent,
    CustomerInvoices,
    CustomerPayments,
    ModalComponent,
    CustomerPaymentForm,
  },
  props: ["customer", "boxs"],
  data() {
    return {
      tabs: ["facturas", "pagos"],
      tab: "facturas",
      invoiceFilter: "pending",
      paymentGroup: "daily",
      showingModal: false,
      lockModal: false,
    };
  },
  methods: {
    closeModal() {
      this.showingModal = false;
      this.lockModal = false;
    },
  },
};
</script>