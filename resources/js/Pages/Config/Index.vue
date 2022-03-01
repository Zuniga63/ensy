<template>
  <app-layout title="Configuración">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Configuración
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <!-- Formulario para información basica -->
        <update-business-information />
        <jet-section-border class="mb-4"/>
        <!-- Formulario para actualizar las redes sociales -->
        <update-social-networks/>
        <jet-section-border class="mb-4"/>
        <commercial-information :config="config"/>
        <jet-section-border class="mb-4"/>

        <div class="hidden lg:block">
          <new-district :departments="departments"/>
          <jet-section-border></jet-section-border>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import UpdateBusinessInformation from "@/Pages/Config/Partial/UpdateBusinessInformation.vue";
import UpdateSocialNetworks from "./Partial/UpdateSocialsAndContacts.vue"
import CommercialInformation from "./Partial/CommercialInformation.vue"
import NewDistrict from "./Partial/NewDistrict.vue";

export default {
  components: {
    AppLayout,
    JetSectionBorder,
    UpdateBusinessInformation,
    NewDistrict,
    UpdateSocialNetworks,
    CommercialInformation,
  },
  props: ["departments", "config"],
  mounted() {
    //console.log(this.$page.props.businessConfig);
  },
};
</script>