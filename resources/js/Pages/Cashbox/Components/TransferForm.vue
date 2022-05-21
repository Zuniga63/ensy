<template>
  <form
    class="w-11/12 lg:w-96 px-4 py-6 border border-gray-300 rounded-lg bg-white shadow overflow-hidden"
    @submit.prevent="submit"
  >
    <header class="border-b-4 border-double border-slate-700 mb-4">
      <h3 class="text-base text-gray-800 font-bold">Registrar una Transferencia</h3>
      <p class="text-sm text-gray-800 text-opacity-95">
        Este formulario permite transferir dinero a otras cajas de la paltaforma.
      </p>
    </header>

    <!-- Body -->
    <div class="pb-4">
      <!-- Selección de la fecha -->
      <div class="flex items-center mb-2">
        <JetCheckbox class="mr-2" v-model:checked="form.setDate" id="setTransferDate" />
        <label for="setTransferDate" class="text-sm text-gray-800">Seleccionar fecha</label>
      </div>

      <!-- Conjunto de controles para la fecha y la hora -->
      <transition
        name="show-date-controllers"
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-90"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-90"
      >
        <!-- Date -->
        <div v-show="form.setDate" class="mb-2">
          <!-- Date -->
          <div class="grid grid-cols-12 items-center">
            <label for="transferDate" class="col-span-3 text-sm text-gray-800">Fecha</label>

            <JetInput
              type="date"
              id="transferDate"
              class="col-span-9 w-full text-sm"
              v-model="form.date"
              :max="form.setDate ? maxDate : null"
            />

            <jet-input-error :message="form.errors.date" class="mt-2 col-span-12" />
          </div>

          <!-- Checkbox for time -->
          <div class="flex items-center mb-2">
            <JetCheckbox name="setTransferTime" id="setTransferTime" class="mr-2" v-model:checked="form.setTime" />
            <label for="setTransferTime" class="text-sm text-gray-800">Seleccionar hora</label>
          </div>

          <transition
            name="show-date-controllers"
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
          >
            <div v-show="form.setTime" class="grid grid-cols-12 items-center">
              <label for="transferTime" class="col-span-3 text-sm text-gray-800">Hora:</label>
              <JetInput
                type="time"
                name="transactionTime"
                class="col-span-9 w-full text-sm"
                id="transferTime"
                v-model="form.time"
              />

              <jet-input-error :message="form.errors.time" class="mt-2 col-span-12" />
            </div>
          </transition>
        </div>
      </transition>

      <!-- boxDestination -->
      <div class="mb-2">
        <label for="boxDestination" class="block mb-2 text-sm text-gray-800">Caja Destino</label>

        <select
          name="boxDestination"
          id="boxDestination"
          v-model="form.boxDestination"
          class="
            block
            w-full
            p-2
            border border-gray-300
            focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            rounded-md
            shadow-sm
            text-sm text-gray-800
          "
        >
          <option :value="null">Selecciona una caja</option>
          <option v-for="box in boxs" :key="box.id" :value="box.id">
            {{ box.name }}
          </option>
        </select>
        <jet-input-error :message="form.errors.boxDestination" class="mt-2" />
      </div>

      <!-- Description -->
      <div class="mb-2">
        <label for="description" class="block mb-2 text-basetext-gray-800 text-center">Descripción</label>
        <textarea
          name="description"
          id="description"
          rows="2"
          class="
            block
            w-full
            p-2
            border border-gray-300
            focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            rounded-md
            shadow-sm
            text-sm text-gray-800
          "
          placeholder="Descripción opcional."
          v-model="form.description"
        ></textarea>
        <jet-input-error :message="form.errors.description" class="mt-2" />
      </div>
      <!-- Importe -->
      <div class="mb-2">
        <label for="amount" class="inline-block mb-1 text-sm text-gray-800">Importe</label>
        <CurrencyInput
          name="amount"
          id="amount"
          type="text"
          class="w-full text-sm text-gray-800 text-right"
          v-model="form.amount"
          placeholder="Importe a transferir."
          autocomplete="off"
        />
        <jet-input-error :message="form.errors.amount" class="col-span-12 mt-2" />
      </div>
    </div>

    <footer class="flex justify-between pt-2 border-t-4 border-double border-slate-900">
      <JetButton :disabled="form.processing">
        <!-- Spin -->
        <spin-icon v-show="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />

        <!-- Message -->
        <span v-show="!form.processing">Transferir</span>
        <span v-show="form.processing">Procesando...</span>
      </JetButton>
      <JetDangerButton type="button" @click="hiddenForm" :disabled="form.processing">Cancelar</JetDangerButton>
    </footer>
  </form>
</template>
<script>
import Swal from "sweetalert2";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetInput from "@/Jetstream/Input.vue";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import SpinIcon from "@/Components/Svgs/Spin.vue";

export default {
  components: {
    JetButton,
    JetDangerButton,
    JetInputError,
    JetCheckbox,
    JetInput,
    CurrencyInput,
    SpinIcon,
  },
  props: {
    cashboxId: {
      type: Number,
    },
    balance: {
      type: Number,
    },
    boxs: {
      type: Array,
    },
    maxDate: {
      type: String,
    },
    lastDate: String,
    lastTime: String,
  },
  setup(props) {
    const form = useForm({
      setDate: props.lastDate ? true : false,
      date: props.lastDate,
      setTime: props.lastTime ? true : false,
      time: props.lastTime,
      boxDestination: null,
      description: null,
      amount: null,
    });

    return { form };
  },
  emits: ["hiddenForm", "lockModal", "unlockModal", "updateTime"],
  methods: {
    hiddenForm() {
      let time = { date: null, time: null };
      if (this.form.setDate) time.date = this.form.date;
      if (this.form.setTime) time.time = this.form.time;

      this.$emit("updateTime", time);
      this.$emit("hiddenForm");
    },
    submit() {
      this.form.post(route("cashbox.storeTransfer", this.cashboxId), {
        preserveScroll: true,
        preserveState: true,
        onStart: () => {
          this.$emit("lockModal");
        },
        onSuccess: (page) => {
          this.hiddenForm();
          let data = page.props.flash.message;
          if (data) {
            let message = `Trasnferencia de efectivo de la caja <b>"${data.senderBox}"</b> a la caja <b>"${data.addresseeBox}"</b> de forma exitosa.`;
            Swal.fire({
              icon: "success",
              title: "¡Transferencia Exitosa!",
              html: message,
            });
          }
        },
        onFinish: () => {
          this.$emit("unlockModal");
        },
      });
    },
  },
};
</script>