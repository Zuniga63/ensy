<template>
  <jet-form-section @submitted="submit">
    <template #title> Actualizar Estado </template>
    <template #description>
      Permite actualizar la información del propietario, el grupo de
      administración, el seguro y las comisiones.
    </template>

    <template #form>
      <!-- Propietario -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label for="buildign-owner" class="mb-2" value="Propietario" />

        <!-- Campo -->
        <select
          name="buildign-owner"
          id="buildign-owner"
          v-model="form.owner_id"
          class="
            w-full
            px-6
            py-2
            border border-gray-300
            focus:border-indigo-300
            focus:ring
            focus:ring-indigo-200
            focus:ring-opacity-50
            rounded-md
            shadow-sm
            text-sm text-gray-800
          "
        >
          <option :value="null">Selecciona un Propietario</option>
          <option
            v-for="customer in customers"
            :key="customer.id"
            :value="customer.id"
          >
            {{ customer.full_name }}
          </option>
        </select>

        <!-- Mensaje de error -->
        <jet-input-error :message="form.errors.owner_id" class="mt-2" />
      </div>

      <div class="col-span-6 lg:col-span-3">
        <sumary-info
          :owner="owner"
          :lease-fee="form.lease_fee"
          :commission="form.commission"
          :insured="form.insured"
          :insurance-commission="form.insurance_commission"
        />
      </div>

      <!-- Canon de arrendamiento  y comission-->
      <div class="col-span-6">
        <div class="grid grid-cols-8 gap-2 items-center">
          <!-- Canon de arrendamiento -->
          <div class="col-span-3">
            <custom-label
              for="building-lease-fee"
              class="inline-block mb-1 text-sm text-gray-800"
              required
              value="Canon"
            />

            <CurrencyInput
              name="building-lease-fee"
              id="building-lease-fee"
              type="text"
              class="w-full text-sm text-gray-800 text-right px-4"
              v-model="form.lease_fee"
              placeholder="Ingresa la cuota de arrendamiento"
              autocomplete="off"
            />

            <jet-input-error
              :message="form.errors.lease_fee"
              class="col-span-12 mt-2"
            />
          </div>
          <!-- Cuota de administración -->
          <div class="col-span-3">
            <custom-label
              for="building-admin-fee"
              class="mb-2"
              :class="{
                'text-opacity-40': !hasAdmin,
              }"
              :required="hasAdmin"
              value="Administración"
            />

            <CurrencyInput
              name="building-admin-fee"
              id="building-admin-fee"
              type="text"
              class="w-full text-sm text-gray-800 text-right px-4"
              :class="{ 'text-opacity-40': !hasAdmin }"
              v-model="form.admin_fee"
              placeholder="Ingresa la cuota de adminsitración."
              autocomplete="off"
              :disabled="!hasAdmin"
            />

            <jet-input-error
              :message="form.errors.admin_fee"
              class="col-span-12 mt-2"
            />
          </div>

          <!-- Comision -->
          <div class="col-span-2">
            <!-- Etiqueta -->
            <custom-label
              for="building-commission"
              class="mb-1"
              value="Comisión [%]"
              required
            />
            <!-- Input -->
            <jet-input
              id="building-commission"
              name="building-commission"
              type="number"
              v-model="form.commission"
              class="w-full text-center text-sm"
              placeholder="0%"
              min="0"
              max="100"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.commission" class="mt-2" />
          </div>
        </div>
      </div>

      <!-- Checker for insured -->
      <div class="col-span-6">
        <div class="flex">
          <!-- Has mandate contract -->
          <label class="flex items-center mr-3">
            <jet-checkbox name="insured" v-model:checked="form.insured" />
            <span class="ml-2 text-sm text-gray-600">¿Está Asegurado?</span>
          </label>
        </div>
      </div>

      <!-- Aseguradora -->
      <div class="col-span-6 lg:col-span-3">
        <!-- Etiqueta -->
        <custom-label
          for="building-insurance-carrier"
          class="mb-2"
          :class="{
            'text-opacity-50': !form.insured,
          }"
          value="Aseguradora"
        />

        <!-- Campo -->
        <jet-input
          type="text"
          id="building-insurance-carrier"
          class="w-full text-gray-800"
          :class="{
            'text-opacity-50': !form.insured,
          }"
          placeholder="Escribe el nombre aquí."
          v-model="form.insurance_carrier"
          :disabled="!form.insured"
        />

        <!-- Mensaje de error -->
        <jet-input-error
          :message="form.errors.insurance_carrier"
          class="mt-2"
        />
      </div>

      <!-- Tipo de seguro y COmisión -->
      <div class="col-span-6 lg:col-span-3">
        <div class="grid grid-cols-3 gap-2">
          <!-- Tipo de seguro -->
          <div class="col-span-2">
            <!-- Etiqueta -->
            <custom-label
              for="building-insurance-type"
              class="mb-2"
              value="Tipo de seguro"
              :class="{
                'text-opacity-50': !form.insured,
              }"
            />

            <!-- Campo -->
            <jet-input
              type="text"
              id="building-insurance-type"
              class="w-full text-gray-800"
              placeholder="Escribe el tipo de seguro."
              v-model="form.insurance_type"
              :class="{
                'text-opacity-50': !form.insured,
              }"
              :disabled="!form.insured"
            />

            <!-- Mensaje de error -->
            <jet-input-error
              :message="form.errors.insurance_type"
              class="mt-2"
            />
          </div>

          <!-- Comision -->
          <div>
            <!-- Etiqueta -->
            <custom-label
              for="building-insurance-commission"
              class="mb-2"
              value="Comisión [%]"
              :class="{
                'text-opacity-50': !form.insured,
              }"
              :required="!form.insured"
            />
            <!-- Input -->
            <jet-input
              id="building-insurance-commission"
              name="building-insurance-commission"
              type="number"
              v-model="form.insurance_commission"
              class="w-full text-center text-gray-800"
              :class="{
                'text-opacity-50': !form.insured,
              }"
              :disabled="!form.insured"
              placeholder="0%"
              min="0"
              max="100"
            />

            <!-- Error -->
            <jet-input-error
              :message="form.errors.insurance_commission"
              class="mt-2"
            />
          </div>
        </div>
      </div>

      <!-- Codigo del inmueble -->
      <div class="col-span-6 lg:col-span-3">
        <div class="grid grid-cols-3 gap-2">
          <!-- Codigo -->
          <div>
            <!-- Etiqueta -->
            <custom-label for="building-code" class="mb-2" value="Codigo" />
            <!-- Input -->
            <jet-input
              id="building-code"
              name="building-code"
              type="number"
              v-model="form.code"
              class="w-full text-center text-sm"
              placeholder="0%"
              min="0"
            />

            <!-- Error -->
            <jet-input-error :message="form.errors.code" class="mt-2" />
          </div>
        </div>
      </div>

      <!-- Checkers -->
      <div class="col-span-6">
        <div class="flex">
          <!-- Has mandate contract -->
          <label class="flex items-center mr-3">
            <jet-checkbox
              name="mandateContract"
              v-model:checked="form.mandate_contract"
            />
            <span class="ml-2 text-sm text-gray-600"
              >Tiene contrato de mandato</span
            >
          </label>

          <!-- Available -->
          <label class="flex items-center mr-3">
            <jet-checkbox name="available" v-model:checked="form.available" />
            <span class="ml-2 text-sm text-gray-600">Inmueble disponible</span>
          </label>
        </div>
      </div>
    </template>

    <template #actions>
      <jet-action-message :on="form.recentlySuccessful" class="mr-3">
        Guardado.
      </jet-action-message>

      <jet-button
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        Guardar
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import LinkButton from "@/Components/Form/LinkButton.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import SumaryInfo from "./SumaryInfo.vue";

export default {
  components: {
    AppLayout,
    JetButton,
    LinkButton,
    JetFormSection,
    JetInput,
    JetInputError,
    CustomLabel,
    JetActionMessage,
    CurrencyInput,
    JetCheckbox,
    SumaryInfo,
  },
  props: {
    building: {
      type: Object,
    },
    customers: {
      type: Array,
      default: [],
    },
    admins: {
      type: Array,
      default: [],
    },
  },
  setup(props) {
    const form = useForm({
      owner_id: props.building.owner_id,
      building_admin_id: props.building.building_admin_id,
      lease_fee: props.building.lease_fee,
      admin_fee: props.building.admin_fee,
      code: props.building.code,
      commission: Math.round(props.building.commission * 100),
      insured: props.building.insured,
      insurance_carrier: props.building.insurance_carrier,
      insurance_type: props.building.insurance_type,
      insurance_commission: props.building.insurance_commission * 100,
      available: props.building.available,
      mandate_contract: props.building.mandate_contract,
    });

    return { form };
  },
  methods: {
    submit() {
      let url = route("building.updateBuildingState", this.building.id);

      if (!this.form.lease_fee) this.form.lease_fee = 0;
      if (!this.form.admin_fee || !this.building.building_admin_id)
        this.form.admin_fee = 0;

      this.form.put(url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          //TODO
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    },
  },
  computed: {
    /**
     * Define si el inmueble tiene administración
     */
    hasAdmin() {
      return this.form.building_admin_id ? true : false;
    },
    /**
     * Recupera la instancia del propietario seleccionada
     */
    owner() {
      if (this.form.owner_id) {
        return this.customers.find((item) => item.id === this.form.owner_id);
      }

      return null;
    },
  },
  beforeUpdate() {
    this.form.building_admin_id = this.building.building_admin_id;
    this.form.admin_fee = this.building.admin_fee;
    this.form.insurance_carrier = this.building.insurance_carrier;
    this.form.insurance_type = this.building.insurance_type;
    this.form.insurance_commission = Math.round(
      this.building.insurance_commission * 100
    );
  },
};
</script>