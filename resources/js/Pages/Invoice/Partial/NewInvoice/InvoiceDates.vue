<template>
  <div class="py-2 px-2 border border-gray-200 rounded-md bg-gray-50 shadow">
    <!-- expedition date -->
    <div class="mb-2">
      <custom-label
        class="mb-1 text-xs text-gray-800 text-center"
        value="Fecha de expedición"
        for="expedition-date"
        required
      />

      <jet-input
        type="date"
        class="w-full py-1 text-xs text-center text-gray-800"
        v-model="expedition"
        :max="expeditionMaxDate"
        @blur="validateDate"
        @change="validateDate"
        ref="expedition"
      />
      <input-error :message="errors.expeditionDate" class="mt-1 text-xs" />
    </div>

    <!-- expiration date -->
    <div>
      <custom-label
        class="mb-1 text-xs text-gray-800 text-center"
        value="Fecha de vencimiento"
        for="expiration-date"
        required
      />

      <jet-input
        type="date"
        class="w-full py-1 text-xs text-center text-gray-800"
        v-model.lazy="expiration"
        :min="expirationMinDate"
        @blur="validateDate"
        @change="validateDate"
        ref="expiration"
      />
      <input-error :message="errors.expirationDate" class="mt-1 text-xs" />
    </div>
  </div>
</template>

<script>
import JetInput from "@/Jetstream/Input.vue";
import CustomLabel from "@/Components/Form/Label.vue";
import dayjs from "dayjs";
import InputError from "@/Components/Form/InputError.vue";

export default {
  components: {
    JetInput,
    CustomLabel,
    InputError,
  },
  props: ["expeditionDate", "expirationDate", "errors"],
  emits: ["update:expeditionDate", "update:expirationDate"],
  data() {
    return {
      expedition: this.expeditionDate,
      oldExpedition: this.expeditionDate ? dayjs(this.expeditionDate) : null,

      expiration: this.expirationDate,

      diff: null,
    };
  },
  methods: {
    /**
     * Este metodo se ejecuta cuando cualquiera de los
     * dos campos de fecha han perdido el foco y se encarga
     * de validar si las fechas son correctas.
     */
    validateDate() {
      //Se obtiene las instancias dayjs para las variables locales
      let expedition = dayjs(this.expedition).isValid()
        ? dayjs(this.expedition)
        : null;
      let expiration = dayjs(this.expiration).isValid()
        ? dayjs(this.expiration)
        : null;

      if (expedition) {
        if (expedition.isSame(this.oldExpedition)) {
          if (expiration && expiration.isBefore(expedition)) {
            expiration = expedition.clone().add(this.diff, "day");
          }
        } else {
          expiration = expedition.clone().add(this.diff, "day");
        } //.end if-else
      } else {
        expiration = null;
      }

      if (expedition && expiration) {
        this.oldExpedition = expedition;
        this.diff = expiration.diff(expedition, "day");
      }

      this.expedition = expedition ? expedition.format("YYYY-MM-DD") : null;
      this.expiration = expiration ? expiration.format("YYYY-MM-DD") : null;

      this.updateModel();
    },
    updateModel() {
      this.$emit("update:expeditionDate", this.expedition);
      this.$emit("update:expirationDate", this.expiration);
    },
  }, //.end methods
  computed: {
    /**
     * Estable la fecha maxima de expedición como el día actual
     */
    expeditionMaxDate() {
      return dayjs().format("YYYY-MM-DD");
    },
    expirationMinDate() {
      if (this.expedition) return this.expedition;
    },
  }, //.end computed
  beforeMount() {
    console.log(this.expeditionDate, this.expedition)
    if (this.expedition && this.expiration) {
      this.diff = dayjs(this.expiration).diff(dayjs(this.expedition), "days");
    } else {
      this.diff = dayjs().daysInMonth();
    }
  },
};
</script>
