<template>
  <label class="block font-medium text-sm text-gray-700" :class="{required}">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot></slot></span>
  </label>
</template>

<script>
export default {
  props: {
    value: String,
    required: Boolean
  },
};
</script>

<style scoped>
.required:after {
  content: " *";
  color: #e73d4a;
}
</style>