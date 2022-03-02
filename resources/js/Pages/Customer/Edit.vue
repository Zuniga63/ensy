<template>
  <app-layout title="Nuevo Cliente">
    <template #header>
      <div class="flex justify-between">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Actualizar Cliente
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('customer.index')"> Regresar </link-button>
      </div>
    </template>

    <!-- CONTENT -->
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <customer-form :customer="customer"/>
      <jet-section-border/>
      <contact-form class="mt-10" :customer="customer"/>
      <jet-section-border/>
      <bank-information-form class="mt-10" :customer="customer"/>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import CustomerForm from "./Partial/CustomerFormBasic.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import BankInformationForm from "./Partial/BankInformationForm.vue";
import ContactForm from "./Partial/ContactForm.vue";

export default {
  components: {
    AppLayout,
    LinkButton,
    CustomerForm,
    JetSectionBorder,
    BankInformationForm,
    ContactForm
  },
  props:{
    customer: Object,
    departments: Array,
  }
};
</script>