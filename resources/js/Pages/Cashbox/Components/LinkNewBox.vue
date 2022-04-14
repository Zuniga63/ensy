<template>
  <Link
    :href="route('cashbox.create')"
    class="
      p-2
      border border-white
      rounded-md
      bg-gray-700
      text-white text-xs text-center
      tracking-wider
      uppercase
      font-bold
      shadow
      transition-colors
      hover:bg-transparent hover:border-gray-700 hover:text-gray-700
    "
  >
    Crear Caja
  </Link>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
export default {
  components: { Link },
};
</script>