<template>
  <div class="hidden lg:flex flex-col">
    <div class="overflow-x-auto">
      <div class="align-middle inline-block min-w-full">
        <div class="shadow overflow-hidden border-b border-gray-200">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <!-- Nombre -->
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Nombre
                </th>
                <!-- Ultima Actividad -->
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Registro de Actividad
                </th>
                <!-- Rol -->
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                >
                  Rol
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Delete</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="shrink-0 h-10 w-10">
                      <img
                        class="h-10 w-10 rounded-full"
                        :src="user.profile_photo_url"
                        alt="user.name"
                      />
                    </div>
                    <div class="ml-4">
                      <div class="flex">
                        <p class="text-sm font-medium text-gray-800">
                          {{ user.name }}
                        </p>
                        <!-- Verificación -->
                        <div
                          v-if="user.email_verified_at"
                          :title="
                            'Verificado: ' +
                            dayjs(user.email_verified_at).fromNow()
                          "
                          class="hover:cursor-help"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-green-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </div>
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ user.email }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    Resgistro: {{ dayjs(user.created_at).fromNow() }}
                  </div>
                  <div class="text-sm text-gray-500">
                    Estado:
                    <span v-if="user.sessions.length">
                      Activo
                      {{
                        dayjs(user.sessions[0].lastActivity * 1000).fromNow()
                      }}
                    </span>
                    <span v-else>Desconectado</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  Admin
                </td>
                <td
                  class="
                    px-6
                    py-4
                    whitespace-nowrap
                    text-right text-sm
                    font-medium
                  "
                >
                  <a
                    href="javascript:;"
                    class="text-red-600 hover:text-red-900"
                    @click="$emit('destroyUser', user)"
                    >
                      Eliminar
                    </a
                  >
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";

export default {
  props: {
    users: Array,
  },
  emits: ['destroyUser'],
  setup(props) {
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);
  },
  methods: {
    dayjs,
  },
};
</script>