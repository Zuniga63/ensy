<template>
  <div class="mb-4">
    <header
      class="
        flex
        p-2
        border border-indigo-900
        rounded-t-lg
        bg-indigo-600 bg-opacity-60
      "
    >
      <img
        :src="user.profile_photo_url"
        :alt="user.name"
        class="inline-block h-14 w-14 mr-3 rounded-full ring-2 ring-indigo-600"
      />
      <div class="flex flex-col">
        <p class="text-base text-gray-800 font-bold">{{ user.name }}</p>
        <p class="text-sm text-gray-900 text-opacity-80">{{ user.email }}</p>
        <p class="text-xs text-gray-900 text-opacity-60" v-if="lastActivity">
          Ultima Actividad: <span class="font-bold">{{ lastActivity }}</span>
        </p>
        <p class="text-xs text-gray-900 text-opacity-60" v-else>
          No tiene actividad reciente.
        </p>
      </div>
    </header>

    <!-- Body -->
    <div
      class="
        px-2
        py-4
        border-l border-r border-indigo-900
        bg-indigo-100 bg-opacity-50
      "
    >
      <p class="text-base">Rol: Administrador</p>
      <p class="text-gray-800 text-opacity-90 text-sm">
        Email verificado: {{ validatedAt }}
      </p>
      <div class="mt-2">
        <p class="text-sm">
          Registro: <span class="font-bold">{{ createdAt }}</span>
        </p>
        <p
          v-if="!updateAndCreateIsSame"
          class="text-xs text-gray-800 text-opacity-70"
        >
          Actualización: <span class="font-bold">{{ updatedAt }}</span>
        </p>
        <p class="text-xs text-gray-800 text-opacity-70" v-else>&nbsp;</p>
      </div>
    </div>

    <footer
      class="
        flex
        justify-between
        p-2
        border border-indigo-900
        bg-indigo-600
        rounded-b-lg
        bg-opacity-60
      "
    >
      <jet-button type="button" class="text-sm">Ver Ficha</jet-button>
      <jet-danger-button type="button">Eliminar</jet-danger-button>
    </footer>
  </div>
</template>
<script>
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";
export default {
  props: {
    user: Object,
  },
  components: {
    JetButton,
    JetDangerButton,
  },
  setup(props) {
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);
  },
  mounted() {
    console.log(this.user);
  },
  computed: {
    createdAt() {
      return dayjs(this.user.created_at).fromNow();
    },
    updatedAt() {
      return dayjs(this.user.updated_at).fromNow();
    },
    updateAndCreateIsSame() {
      return dayjs(this.user.created_at).isSame(dayjs(this.user.updated_at));
    },
    validatedAt() {
      if (this.user.email_verified_at) {
        return dayjs(this.user.email_verified_at).fromNow();
      }

      return "No";
    },
    lastActivity() {
      if (this.user.sessions.length > 0) {
        return dayjs(this.user.sessions[0].lastActivity * 1000).fromNow();
      }

      return null;
    },
  },
};
</script>