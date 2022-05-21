<template>
  <form
    class="w-full px-4 py-6 border border-gray-300 rounded-lg bg-white shadow overflow-hidden"
    @submit.prevent="submit"
  >
    <header class="border-b-4 border-double border-slate-700 mb-4">
      <h3 class="text-base text-gray-800 font-bold">
        <span v-show="!updateForm">Registrar Transacción</span>
        <span v-show="updateForm">Actualizar Transacción</span>
      </h3>
      <p class="text-sm text-gray-800 text-opacity-95">
        <span v-show="!updateForm">Permite guardar una nueva transacción en la base de datos.</span>
        <span v-show="updateForm">Actualiza la transacción en la base de datos.</span>
      </p>
    </header>

    <!-- Body -->
    <div class="pb-4">
      <!-- Selección de la fecha Mobil -->
      <div class="block lg:hidden">
        <div class="flex items-center mb-2">
          <JetCheckbox class="mr-2" v-model:checked="form.setDate" id="setDate" />
          <label for="setDate" class="text-sm text-gray-800">Seleccionar fecha</label>
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
              <label for="transactionDate" class="col-span-3 text-sm text-gray-800">Fecha</label>

              <JetInput
                type="date"
                id="transactionDate"
                class="col-span-9 w-full text-sm"
                v-model="form.date"
                :max="form.setDate ? maxDate : null"
              />

              <jet-input-error :message="form.errors.date" class="mt-2 col-span-12" />
            </div>

            <!-- Checkbox for time -->
            <div class="flex items-center mb-2">
              <JetCheckbox name="setDate" id="setTime" class="mr-2" v-model:checked="form.setTime" />
              <label for="setTime" class="text-sm text-gray-800">Seleccionar hora</label>
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
                <label for="transactionTime" class="col-span-3 text-sm text-gray-800">Hora: </label>
                <JetInput
                  type="time"
                  name="transactionTime"
                  class="col-span-9 w-full text-sm"
                  id="transactionTime"
                  v-model="form.time"
                />

                <jet-input-error :message="form.errors.time" class="mt-2 col-span-12" />
              </div>
            </transition>
          </div>
        </transition>
      </div>

      <!-- SELECTOR DE LA FECHA ESCRITORIO -->
      <div class="hidden lg:grid grid-cols-3 gap-2 mb-4">
        <!-- Selector de fecha -->
        <div class="col-span-2">
          <!-- Checked for setDate -->
          <div class="flex items-center mb-2">
            <JetCheckbox class="mr-2" v-model:checked="form.setDate" id="setDateDesk" />
            <label for="setDateDesk" class="text-sm text-gray-800">Seleccionar fecha</label>
          </div>

          <!-- Input para la fecha -->
          <div class="flex flex-col">
            <JetInput
              type="date"
              id="transactionDateDesktop"
              class="w-full text-sm text-gray-800 transition-colors"
              :class="{
                'text-opacity-20 border-opacity-20': !form.setDate,
              }"
              v-model="form.date"
              :max="form.setDate ? maxDate : null"
              :disabled="!form.setDate"
            />

            <jet-input-error :message="form.errors.date" class="mt-2" />
          </div>
        </div>

        <!-- Selector de la hora -->
        <div>
          <!-- Checked for setTime -->
          <div class="flex items-center mb-2">
            <JetCheckbox
              class="mr-2"
              :class="{ 'text-opacity-20': !form.setDate }"
              v-model:checked="form.setTime"
              id="setTimeDesk"
              :disabled="!form.setDate"
            />
            <label for="setTimeDesk" class="text-sm text-gray-800" :class="{ 'text-opacity-20': !form.setDate }"
              >Hora</label
            >
          </div>
          <!-- Slector de hora -->
          <div class="flex flex-col">
            <JetInput
              type="time"
              name="transactionTime"
              class="col-span-9 w-full text-sm text-gray-800 transition-colors"
              :class="{
                'text-opacity-20 border-opacity-20': !form.setTime || !form.setDate,
              }"
              v-model="form.time"
              :disabled="!form.setDate || !form.setTime"
            />

            <jet-input-error :message="form.errors.time" class="mt-2" />
          </div>
        </div>
      </div>

      <!-- Description -->
      <div class="mb-2">
        <label for="description" class="block mb-2 text-base text-gray-800 text-center">Descripcion</label>
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
          placeholder="Escribe la descripción."
          v-model="form.description"
        ></textarea>
        <jet-input-error :message="form.errors.description" class="mt-2" />
      </div>

      <!-- Tipo de Transacción -->
      <div class="hidden lg:block mb-2">
        <label for="typeSelector" class="block mb-2 text-sm text-gray-800 text-left"> Tipo de Transación </label>

        <select
          name="typeSelector"
          id="typeSelector"
          class="
            w-full
            border border-gray-300
            rounded
            text-sm text-gray-800
            focus:ring focus:ring-indigo-500 focus:ring-opacity-40
          "
          v-model="form.type"
        >
          <option value="income">Ingreso en efectivo</option>
          <option value="expense">Egreso en efectivo</option>
        </select>

        <jet-input-error :message="form.errors.type" class="mt-2" />
      </div>

      <!-- Importe -->
      <div class="mb-2">
        <label for="transactionAmount" class="inline-block mb-1 text-sm text-gray-800">Importe</label>
        <CurrencyInput
          name="amount"
          id="transactionAmount"
          type="text"
          class="w-full text-sm text-gray-800 text-right px-4"
          v-model="form.amount"
          placeholder="Ingresa el importe"
          autocomplete="off"
        />
        <jet-input-error :message="form.errors.amount" class="col-span-12 mt-2" />
      </div>

      <!-- Tipo de transacción -->
      <div class="lg:hidden flex justify-around">
        <div class="flex items-center">
          <input
            type="radio"
            name="income"
            id="transactionIncome"
            v-model="form.type"
            value="income"
            class="mr-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
          <label for="transactionIncome">Ingreso</label>
        </div>

        <div class="flex items-center">
          <input
            type="radio"
            name="expense"
            id="transactionExpense"
            v-model="form.type"
            value="expense"
            class="mr-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          />
          <label for="transactionExpense">Egreso</label>
        </div>
      </div>
      <jet-input-error :message="form.errors.type" class="mt-2 lg:hidden" />
    </div>

    <footer class="flex justify-between pt-2 border-t-4 border-double border-slate-900">
      <JetDangerButton type="button" @click="hiddenForm" :disabled="form.processing">Cancelar</JetDangerButton>
      <JetButton :disabled="form.processing">
        <spin-icon v-show="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
        {{ buttonMessage }}
      </JetButton>
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
    maxDate: {
      type: String,
    },
    transaction: {
      type: Object,
      default: null,
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
      description: null,
      amount: null,
      type: "income",
    });

    return { form };
  },
  emits: ["hiddenForm", "lockModal", "unlockModal", "updateTime"],
  data() {
    return {
      updateForm: false,
      buttonMessage: "Registrar",
      processing: false,
    };
  },
  methods: {
    hiddenForm() {
      let time = { date: null, time: null };
      if (this.form.setDate) time.date = this.form.date;
      if (this.form.setTime) time.time = this.form.time;
      this.$emit("updateTime", time);
      this.$emit("hiddenForm");
      /* this.form.reset("description", "amount", "type");
      this.form.setDate = this.form.setDate && this.form.date ? true : false;
      this.form.setTime = this.form.setTime && this.form.time ? true : false;
      this.updateForm = false; */
      /* this.buttonMessage = "Registrar"; */
    },
    submit() {
      let url = route("cashbox.storeTransaction", this.cashboxId);
      let method = "post";

      if (this.updateForm) {
        let routeName = "cashbox.updateTransaction";
        let routeParameters = [this.transaction.cashbox_id, this.transaction.id];
        url = route(routeName, routeParameters);
        method = "put";
      }

      this.form.submit(method, url, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => {
          this.buttonMessage = !this.updateForm ? "Registrando..." : "Actualizando...";
          this.$emit("lockModal");
        },
        onSuccess: (page) => {
          let title = !this.updateForm ? "¡Registro Exitoso!" : "¡Actualización Exitosa!";
          let message = page.props.flash.message;
          this.hiddenForm();
          if (message) {
            Swal.fire({
              icon: "success",
              title,
              text: message,
            });
          }
        },
        onFinish: () => {
          this.buttonMessage = !this.updateForm ? "Registrar" : "Actualizar";
          this.$emit("unlockModal");
        },
      });
    },
  },
  beforeMount() {
    if (this.transaction) {
      this.form.setDate = true;
      this.form.date = this.transaction.date.format("YYYY-MM-DD");
      this.form.setTime = true;
      this.form.time = this.transaction.date.format("HH:mm");
      this.form.description = this.transaction.description;
      this.form.type = this.transaction.amount >= 0 ? "income" : "expense";
      this.form.amount = Math.abs(this.transaction.amount);
      this.updateForm = true;
      this.buttonMessage = "Actualizar";
    }
  },
};
</script>