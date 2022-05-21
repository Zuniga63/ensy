<template>
  <form
    class="
      col-span-1
      rounded
      bg-gradient-to-b
      from-emerald-500
      to-cyan-500
      overflow-hidden
      shadow
    "
    @submit.prevent="submit"
  >
    <header class="px-2 py-4 mb-3 border-b-2 border-white">
      <h2
        class="text-sm text-white text-center font-bold uppercase tracking-wide"
      >
        Registrar Actividad
      </h2>
    </header>

    <div class="px-4 py-2 mb-3">
      <!-- Title -->
      <div class="mb-2">
        <label for="title" class="col-span-3 text-xs text-white">Titulo</label>
        <jet-input
          type="text"
          name="title"
          id="title"
          v-model="title"
          class="w-full text-xs text-gray-800"
          placeholder="Escribe el titulo."
          ref="input"
        />
        <jet-input-error
          :message="errors.title"
          class="px-2 py-1 mt-2 rounded bg-red-50"
        />
      </div>

      <!-- Start Activity -->
      <div class="grid grid-cols-3 gap-2 mb-2">
        <label for="fromDate" class="col-span-3 text-xs text-white"
          >Inicio de actividad</label
        >
        <jet-input
          type="date"
          name="fromDate"
          id="fromDate"
          class="col-span-2 text-xs text-gray-800"
          v-model="fromDate"
          @input="changeDate"
        />
        <jet-input
          type="time"
          v-model="fromTime"
          class="text-xs text-gray-800"
          @input="changeDate"
        />

        <jet-input-error
          :message="errors.fromDate"
          class="col-span-3 px-4 py-1 rounded bg-red-50"
        ></jet-input-error>
      </div>

      <!-- End activity -->
      <div class="grid grid-cols-3 gap-2 mb-2">
        <label for="toDate" class="col-span-3 text-xs text-white"
          >Finalización</label
        >
        <jet-input
          type="date"
          name="toDate"
          id="toDate"
          v-model="toDate"
          class="col-span-2 text-xs text-gray-800"
        />
        <jet-input type="time" v-model="toTime" class="text-xs text-gray-800" />

        <jet-input-error
          :message="errors.toDate"
          class="col-span-3 px-4 py-1 rounded bg-red-50"
        />
      </div>

      <!-- Description -->
      <div>
        <label for="observation" class="inline-block text-xs text-white"
          >Observación</label
        >
        <textarea
          cols="30"
          rows="2"
          id="observation"
          placeholder="Escribe una descripción (es opcional)"
          v-model="description"
          class="
            w-full
            border border-gray-200
            rounded
            text-xs text-gray-800
            focus:ring focus:ring-indigo-500 focus:ring-opacity-20
          "
        ></textarea>

        <jet-input-error
          :message="errors.description"
          class="px-4 py-1 mt-2 rounded bg-red-50"
        />
      </div>
    </div>

    <footer class="px-4 py-4 border-t border-white">
      <div class="flex justify-between">
        <input
          type="button"
          value="Cancelar"
          class="
            px-4
            py-1
            border border-transparent
            rounded
            bg-red-600
            focus:ring
            focus:ring-red-200
            focus:outline-none
            focus:border-red-700
            hover:bg-red-500
            active:bg-red-600
            disabled:opacity-25
            text-sm text-white
            font-semibold
            tracking-wider
            shadow
            transition
          "
          :disabled="wait"
          @click="$emit('cancel')"
        />
        <input
          type="submit"
          value="Agregar"
          class="
            px-4
            py-1
            border border-transparent
            rounded
            bg-emerald-600
            focus:ring
            focus:ring-emerald-200
            focus:outline-none
            focus:border-emerald-700
            hover:bg-emerald-500
            active:bg-emerald-600
            disabled:opacity-25
            text-sm text-white
            font-semibold
            tracking-wider
            shadow
            transition
          "
          :disabled="wait"
        />
      </div>
    </footer>
  </form>
</template>

<script>
import JetInputError from "@/Jetstream/InputError.vue";
import JetInput from "@/Jetstream/Input.vue";

export default {
  components: { JetInputError, JetInput },
  props: ["lastDate", "lastTime"],
  emits: ["addActivity", "wait", "finish", "cancel"],
  data() {
    return {
      fromDate: this.lastDate,
      fromTime: this.lastTime,
      toDate: this.lastDate,
      toTime: this.lastTime,
      title: null,
      description: null,
      wait: false,
      errors: {
        fromDate: null,
        toDate: null,
        title: null,
        description: null,
      },
    };
  }, //.end data
  methods: {
    changeDate() {
      this.toDate = this.fromDate;
      this.toTime = this.fromTime;
    },
    getData() {
      return {
        fromDate: this.fromDate + " " + this.fromTime,
        toDate: this.toDate + " " + this.toTime,
        title: this.title,
        description: this.description,
      };
    },
    async submit() {
      this.wait = true;
      this.$emit("wait");

      let data = this.getData();
      let url = route("dailyActivity.store");

      try {
        const res = await axios.post(url, data);
        this.fromDate = this.toDate;
        this.fromTime = this.toTime;
        this.title = null;
        this.description = null;
        this.$emit("addActivity", res.data);
      } catch (error) {
        if (error.response) {
          if (error.response.data?.errors) {
            for (const key in this.errors) {
              if (Object.hasOwnProperty.call(error.response.data.errors, key)) {
                this.errors[key] = error.response.data.errors[key][0];
              } else {
                this.errors[key] = null;
              }
            }
          }
        } else if (error.request) {
          // La petición fue hecha pero no se recibió respuesta
          // `error.request` es una instancia de XMLHttpRequest en el navegador y una instancia de
          // http.ClientRequest en node.js
          console.log(error.request);
        } else {
          // Algo paso al preparar la petición que lanzo un Error
          console.log("Error", error.message);
        }
      } finally {
        this.wait = false;
        this.$emit("finish");
      }
    },
  },
  mounted() {
    this.$refs.input.focus();
  },
};
</script>
