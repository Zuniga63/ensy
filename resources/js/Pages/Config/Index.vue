<template>
  <app-layout title="Configuración">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Configuración
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <update-business-information></update-business-information>
        <jet-section-border></jet-section-border>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import UpdateBusinessInformation from "@/Pages/Config/Partial/UpdateBusinessInformation.vue";

export default {
  components: {
    AppLayout,
    JetSectionBorder,
    UpdateBusinessInformation,
  },
  mounted() {
    // console.log(this.$page.props.businessConfig);
  },
};
</script>