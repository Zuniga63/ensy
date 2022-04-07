<template>
  <transition
    enter-active-class="ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-show="show"
      class="
        absolute
        inset-0
        flex
        items-center
        justify-center
        bg-transparent
        backdrop-blur-sm
        transition-opacity
        z-50
      "
    >
      <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      >
        <div
          class="
            p-3
            border border-gray-200
            rounded-md
            bg-gray-200
            transform
            transition-all
          "
          v-show="show"
        >
          <!-- Spin -->
          <div class="flex justify-center px-4 py-2 mb-2 bg-white">
            <svg
              class="animate-spin h-16 w-16 text-gray-800"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
          </div>

          <p class="text-gray-600 animate-pulse">Recuperando informacion...</p>
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