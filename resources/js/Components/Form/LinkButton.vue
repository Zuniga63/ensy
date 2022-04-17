<template>
  <Link
    :href="href"
    as="button"
    class="
      block
      px-4
      py-2
      border border-white
      rounded-md
      bg-gray-700
      font-bold
      text-white text-center text-xs
      sm:text-sm
      lg:text-base
      tracking-wider
      uppercase
      transition-colors
    "
    :class="classes"
    :disabled="disabled"
  >
    <slot />
  </Link>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Link,
  },
  props: {
    href: String,
    disabled: Boolean,
  },
  computed: {
    classes() {
      return this.disabled
        ? "bg-opacity-50"
        : "bg-opacity-100 hover:bg-transparent hover:text-gray-700 hover:border-gray-700";
    },
  },
};
</script>