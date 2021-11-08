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
                <!-- Usuario Verificado -->
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
                  Verificado
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
                  Ultima Actividad
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
                <!-- <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Edit</span>
                </th> -->
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img
                        class="h-10 w-10 rounded-full"
                        :src="user.profile_photo_url"
                        alt="user.name"
                      />
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ user.name }}
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
                    Email verificado:
                    <span v-if="user.email_verified_at">
                      {{ dayjs(user.email_verified_at).fromNow() }}
                    </span>
                    <span v-else>No</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-800">
                    <span v-if="user.sessions.length">
                      {{
                        dayjs(user.sessions[0].lastActivity * 1000).fromNow()
                      }}
                    </span>
                    <span class="text-gray-500" v-else>
                      No tiene sessiones activas.
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  Admin
                </td>
                <!-- <td
                  class="
                    px-6
                    py-4
                    whitespace-nowrap
                    text-right text-sm
                    font-medium
                  "
                >
                  <a href="#" class="text-indigo-600 hover:text-indigo-900"
                    >Edit</a
                  >
                </td> -->
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
  setup(props) {
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);
  },
  methods: {
    dayjs,
  },
};
</script>