<template>
  <div>
    <h3 class="mb-2 text-center text-sm text-white font-bold tracking-wider">
      {{ title }}
    </h3>
    <ul class="">
      <li
        v-for="(report, index) in report.reports"
        :key="index"
        class="p-1 border border-gray-200 rounded bg-white shadow mb-1"
      >
        <h3 class="tex-sm text-gray-800 font-bold">
          {{ report.title }}
        </h3>
        <p class="text-xs text-gray-600">
          <span v-if="report.hours > 0">{{ report.hours }}h:</span>
          <span>{{ report.minutes }}min</span> &nbsp;
          <span> ({{ report.percentage }}% - {{ report.accumulated }}%)</span>
        </p>
      </li>
    </ul>

    <div
      class="
        w-full
        sticky
        bottom-0
        p-2
        rounded
        bg-gradient-to-b
        from-emerald-500
        to-white
      "
    >
      <h4 class="text-xs text-gray-800 text-center font-bold tracking-wide">
        Resumen
      </h4>
      <!-- Sleep -->
      <p class="text-xs text-gray-800">
        Dormir:
        <span class="font-bold"> {{ report.sleep.hours }} h </span>
        :
        <span class="font-bold">{{ report.sleep.minutes }} min</span>
        &nbsp;
        <span class="italy"> {{ report.sleep.percentage }}% </span>
      </p>
      <!-- Workin -->
      <p class="text-xs text-gray-800">
        Work:
        <span class="font-bold"> {{ report.workingTime.hours }} h </span>
        :
        <span class="font-bold">{{ report.workingTime.minutes }} min</span>
        &nbsp;
        <span class="italy"> {{ report.workingTime.percentage }}% </span>
      </p>
      <!-- Free -->
      <p class="text-xs text-gray-800">
        Free:
        <span class="font-bold"> {{ report.unassignedTime.hours }} h </span>
        :
        <span class="font-bold">{{ report.unassignedTime.minutes }} min</span>
        &nbsp;
        <span class="italy"> {{ report.unassignedTime.percentage }}% </span>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  props: ["title", "report"],
};
</script>

<style>
</style>