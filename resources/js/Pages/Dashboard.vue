<template>
  <app-layout title="Panel">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div>
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
              <h1 class="text-2xl mb-6">¡Bienvenido, {{ $page.props.user.name }}!</h1>
              <p class="text-gray-500">Esta sección aún se encuentra en construcción.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
  components: {
    AppLayout,
  },
};
</script>
