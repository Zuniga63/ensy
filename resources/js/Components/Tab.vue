<template>
  <div class="min-h-full p-4 border border-gray-200 rounded bg-white shadow">
    <!-- Tabs and Controls -->
    <div class="flex justify-between items-center">
      <!-- Tabs -->
      <ul class="flex flex-wrap list-none mb-2">
        <li v-for="(tab, index) in tabs" :key="index">
          <a
            href="javascript:;"
            class="
              block
              px-1 sm:px-2 lg:px-3
              py-3
              border-x-0 border-t-0 border-b-2
              rounded-t
              font-bold
              text-xs
              uppercase
              hover:border-indigo-600 hover:bg-gray-100
              leading-tight
              transition-colors
              focus:border-indigo-600 focus:bg-indigo-50 focus:text-indigo-500
            "
            :class="{
              'text-indigo-500 border-indigo-500': tab === tabSelected,
              'text-gray-800': tab !== tabSelected,
            }"
            @click.stop="$emit('selectTab', tab)"
          >
            {{ tab }}
          </a>
        </li>
      </ul>

      <!-- Controls -->
      <div v-if="$slots.controls">
        <slot name="controls"></slot>
      </div>
    </div>

    <!-- Content -->
    <div class="relative overflow-x-hidden">
      <transition-group
        enter-active-class="ease-out duration-300 transition"
        enter-from-class="opacity-0 translate-x-full"
        enter-to-class="opacity-100 translate-x-0"
        leave-active-class="ease-out duration-200 transition hidden"
        leave-from-class="opacity-100 translate-x-0"
        leave-to-class="opacity-0 translate-x-full"
      >
        <div v-for="(tab, index) in tabs" :key="index" class="w-full" v-show="tab === tabSelected">
          <slot :name="tab"></slot>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    tabs: Array,
    tabSelected: String,
  },
  emits: ["selectTab"],
};
</script>
