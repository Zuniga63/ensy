<template>
  <app-layout>
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
        <customer-info :customer="customer" class="static lg:sticky top-4 col-span-3 lg:col-span-1" />
        <div class="col-span-3 lg:col-span-2 w-full">
          <tab-component :tabs="tabs" :tabSelected="tab" @selectTab="tab = $event">
            <template #facturas>
              <customer-invoices :invoices="customer.invoices" />
            </template>

            <template #pagos>
              <customer-payments :payments="customer.invoice_payments" />
            </template>
          </tab-component>
        </div>
      </div>
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

export default {
  components: { AppLayout, LinkButton, CustomerInfo, TabComponent, CustomerInvoices, CustomerPayments },
  props: ["customer"],
  data() {
    return {
      tabs: ["facturas", "pagos"],
      tab: "facturas",
    };
  },
};
</script>