<template>
  <div
    class="relative col-span-6 border pt-6 px-2 pb-4 border-gray-200 rounded-sm"
  >
    <header
      class="
        absolute
        top-0
        px-4
        border border-gray-200
        rounded-full
        bg-white
        text-sm text-gray-800
        font-bold
        transform
        -translate-y-1/2
      "
    >
      {{ title }}
    </header>

    <slot></slot>
  </div>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
      default: "Titulo",
    },
  },
};
</script>