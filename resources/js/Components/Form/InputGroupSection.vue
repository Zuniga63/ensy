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
        text-sm
        font-bold
        transform
        -translate-y-1/2
        hover:cursor-pointer
      "
      :class="{
        'text-gray-800 bg-white': visible,
        'text-white bg-gray-600': !visible,
      }"
      @click="visible = !visible"
    >
      {{ title }}
    </header>

    <div class="w-full overflow-hidden">
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-y-full"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-300"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-full"
      >
        <div v-show="visible">
          <slot />
        </div>
      </transition>
    </div>
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
  data() {
    return {
      visible: true,
    };
  },
};
</script>