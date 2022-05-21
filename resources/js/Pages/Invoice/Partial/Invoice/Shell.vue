<template>
  <transition leave-active-class="duration-200">
    <div
      class="absolute inset-0 overflow-y-auto px-4 py-2"
      scroll-region
      v-show="show"
    >
      <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-show="show" class="absolute inset-0 transform transition-all">
          <div
            class="absolute inset-0 bg-white opacity-20 backdrop-blur-sm"
          ></div>
        </div>
      </transition>

      <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      >
        <div
          v-show="show"
          class="
            w-full
            mb-6
            bg-white
            rounded-lg
            overflow-hidden
            shadow-xl
            transform
            transition-all
          "
        >
          <slot v-if="show"></slot>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script>
export default {
  props: ["show"],
};
</script>

<style>
</style>