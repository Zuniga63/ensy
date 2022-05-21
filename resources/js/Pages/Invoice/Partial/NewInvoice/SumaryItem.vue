<template>
  <div class="border border-gray-200 bg-white rounded-sm">
    <p class="text-xs text-gray-600 text-center tracking-wide italic">
      {{ title }}
    </p>
    <p class="text-xs text-gray-800 text-center font-medium tracking-wider">
      {{ amount }}
    </p>
  </div>
</template>

<script>
export default {
  props: ["title", "amount"],
};
</script>