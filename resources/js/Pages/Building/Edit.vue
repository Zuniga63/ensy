<template>
  <app-layout title="Registrar Inmueble">
    <template #header>
      <div class="flex justify-between w-full">
        <!-- TITLE OF HEADER -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Actualiza Inmueble
        </h2>

        <!-- BUTTON FOR ADD CUSTOMER -->
        <link-button :href="route('building.index')"> Regresar </link-button>
      </div>
    </template>

    <!-- Body -->
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <general-form :building="building"/>
      <jet-section-border class="mb-4"/>
      <address-form :building="building" :departments="departments" :allDistricts="allDistricts" :admins="admins"/>
      <jet-section-border class="mb-4"/>
      <building-state-form :building="building" :customers="customers" :admins="admins"/>

    </div>
  </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import GeneralForm from "./Partial/GeneralForm.vue";
import AddressForm from "./Partial/AddressForm.vue";
import BuildingStateForm from "./Partial/BuildingStateForm.vue";

export default {
  components:{
    AppLayout,
    LinkButton,
    GeneralForm,
    JetSectionBorder,
    AddressForm,
    BuildingStateForm,
  },
  props:{
    building: Object,
    admins: Array,
    customers: Array,
    departments: Array,
    allDistricts: Array,
  }
}
</script>