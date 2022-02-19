<template>
  <form
    class="
      w-11/12
      lg:w-4/12
      px-4
      py-6
      border border-gray-300
      rounded-lg
      bg-white
      shadow
      overflow-hidden
    "
    @submit.prevent="submit"
  >
    <header class="border-b-4 border-double border-slate-700 mb-4">
      <h3 class="text-base text-gray-800 font-bold">
        <span v-show="!updateForm">Registrar Transacción</span>
        <span v-show="updateForm">Actualizar Transacción</span>
      </h3>
      <p class="text-sm text-gray-800 text-opacity-95">
        <span v-show="!updateForm"
          >Permite guardar una nueva transacción en la base de datos.</span
        >
        <span v-show="updateForm"
          >Actualiza la transacción en la base de datos.</span
        >
      </p>
    </header>

    <!-- Body -->
    <div class="pb-4">
      <!-- Selección de la fecha Mobil -->
      <div class="block lg:hidden">
        <div class="flex items-center mb-2">
          <JetCheckbox
            class="mr-2"
            v-model:checked="form.setDate"
            id="setDate"
          />
          <label for="setDate" class="text-sm text-gray-800"
            >Seleccionar fecha</label
          >
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
              <label
                for="transactionDate"
                class="col-span-3 text-sm text-gray-800"
                >Fecha</label
              >

              <JetInput
                type="date"
                id="transactionDate"
                class="col-span-9 w-full text-sm"
                v-model="form.date"
                :max="form.setDate ? maxDate : null"
              />

              <jet-input-error
                :message="form.errors.date"
                class="mt-2 col-span-12"
              />
            </div>

            <!-- Checkbox for time -->
            <div class="flex items-center mb-2">
              <JetCheckbox
                name="setDate"
                id="setTime"
                class="mr-2"
                v-model:checked="form.setTime"
              />
              <label for="setTime" class="text-sm text-gray-800"
                >Seleccionar hora</label
              >
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
                <label
                  for="transactionTime"
                  class="col-span-3 text-sm text-gray-800"
                  >Hora:
                </label>
                <JetInput
                  type="time"
                  name="transactionTime"
                  class="col-span-9 w-full text-sm"
                  id="transactionTime"
                  v-model="form.time"
                />

                <jet-input-error
                  :message="form.errors.time"
                  class="mt-2 col-span-12"
                />
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
            <JetCheckbox
              class="mr-2"
              v-model:checked="form.setDate"
              id="setDateDesk"
            />
            <label for="setDateDesk" class="text-sm text-gray-800"
              >Seleccionar fecha</label
            >
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
            <label
              for="setTimeDesk"
              class="text-sm text-gray-800"
              :class="{ 'text-opacity-20': !form.setDate }"
              >Seleccionar Hora</label
            >
          </div>
          <!-- Slector de hora -->
          <div class="flex flex-col">
            <JetInput
              type="time"
              name="transactionTime"
              class="col-span-9 w-full text-sm text-gray-800 transition-colors"
              :class="{
                'text-opacity-20 border-opacity-20':
                  !form.setTime || !form.setDate,
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
        <label
          for="description"
          class="block mb-2 text-base text-gray-800 text-center"
          >Descripcion</label
        >
        <textarea
          name="description"
          id="description"
          rows="2"
          class="
            block
            w-full
            p-2
            border border-gray-300
            focus:border-indigo-300
            focus:ring
            focus:ring-indigo-200
            focus:ring-opacity-50
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
        <label
          for="typeSelector"
          class="block mb-2 text-sm text-gray-800 text-left"
        >
          Tipo de Transación
        </label>

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
        <label
          for="transactionAmount"
          class="inline-block mb-1 text-sm text-gray-800"
          >Importe</label
        >
        <CurrencyInput
          name="amount"
          id="transactionAmount"
          type="text"
          class="w-full text-sm text-gray-800 text-right px-4"
          v-model="form.amount"
          placeholder="Ingresa el importe"
          autocomplete="off"
        />
        <jet-input-error
          :message="form.errors.amount"
          class="col-span-12 mt-2"
        />
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
            class="
              mr-2
              border-gray-300
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
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
            class="
              mr-2
              border-gray-300
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
          />
          <label for="transactionExpense">Egreso</label>
        </div>
      </div>
      <jet-input-error :message="form.errors.type" class="mt-2 lg:hidden" />
    </div>

    <footer
      class="
        flex
        justify-between
        pt-2
        border-t-4 border-double border-slate-900
      "
    >
      <JetDangerButton type="button" @click="hiddenForm"
        >Cancelar</JetDangerButton
      >
      <JetButton>
        <svg
          class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          v-show="processing"
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

export default {
  components: {
    JetButton,
    JetDangerButton,
    JetInputError,
    JetCheckbox,
    JetInput,
    CurrencyInput,
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
  },
  setup(props) {
    const form = useForm({
      setDate: false,
      date: null,
      setTime: false,
      time: null,
      description: null,
      amount: null,
      type: "income",
    });

    return { form };
  },
  emits: ["hiddenForm"],
  data() {
    return {
      updateForm: false,
      buttonMessage: "Registrar",
      processing: false,
    };
  },
  methods: {
    hiddenForm() {
      this.$emit("hiddenForm");
      this.form.reset("description", "amount", "type");
      this.form.setDate = this.form.date ? true : false;
      this.form.setTime = this.form.time ? true : false;
      this.updateForm = false;
      this.buttonMessage = "Registrar";
    },
    submit() {
      if (this.updateForm) {
        let routeName = "cashbox.updateTransaction";
        let routeParameters = [
          this.transaction.cashbox_id,
          this.transaction.id,
        ];
        let url = route(routeName, routeParameters);
        console.log(url);
        this.form.put(url, {
          preserveScroll: true,
          preserveState: true,
          onStart: () => {
            this.buttonMessage = "Actualizando...";
            this.processing = true;
          },
          onSuccess: (page) => {
            this.hiddenForm();
            let message = page.props.flash.message;
            if (message) {
              Swal.fire({
                icon: "success",
                title: "¡Actualización Exitosa!",
                text: message,
              });
            }
          },
          onFinish: () => {
            this.buttonMessage = this.updateForm ? "Actualizar" : "Registrar";
            this.processing = false;
          },
        });
      } else {
        this.form.post(route("cashbox.storeTransaction", this.cashboxId), {
          preserveScroll: true,
          preserveState: true,
          onStart: () => {
            this.buttonMessage = "Registrando...";
            this.processing = true;
          },
          onSuccess: (page) => {
            this.hiddenForm();
            let message = page.props.flash.message;
            if (message) {
              Swal.fire({
                icon: "success",
                title: "¡Registro Exitoso!",
                text: message,
              });
            }
          },
          onFinish: () => {
            this.buttonMessage = "Registrar";
            this.processing = false;
          },
        });
      }
    },
  },
  watch: {
    transaction(newTransaction, oldTransaction) {
      if (newTransaction) {
        this.form.setDate = true;
        this.form.date = newTransaction.date.format("YYYY-MM-DD");
        this.form.setTime = true;
        this.form.time = newTransaction.date.format("HH:mm");
        this.form.description = newTransaction.description;
        this.form.type = newTransaction.amount >= 0 ? "income" : "expense";
        this.form.amount = Math.abs(newTransaction.amount);
        this.updateForm = true;
        this.buttonMessage = "Actualizar";
      }
    },
  },
};
</script>