<template>
  <app-layout title="Usuarios">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Administración de Usuarios
      </h2>
    </template>

    <div>
      <!-- Contenedor Principal -->
      <div class="max-w-7xl pt-5 lg:py-10 sm:px-6 lg:px-8 mx-auto shadow">
        <!-- Tarjetas de usuarios -->
        <div
          class="
            relative
            max-h-screen
            sm:rounded
            lg:rounded-md
            overflow-x-hidden
            overscroll-y-auto
          "
        >
          <header
            class="
              sticky
              top-0
              block
              sm:grid sm:grid-cols-2
              lg:grid-cols-3
              sm:items-center
              p-2
              sm:px-4
              border-b border-blue-300
              bg-blue-200
              shadow
            "
          >
            <p class="hidden sm:block lg:col-span-2 text-gray-800 text-lg">
              Listado de Usuarios
            </p>
            <!-- Buscador -->
            <div
              class="
                grid grid-cols-12
                border border-gray-400
                rounded
                overflow-hidden
              "
            >
              <div class="col-span-10">
                <input
                  type="text"
                  class="block w-full p-2 focus:outline-none border-none"
                  placeholder="Buscar usuario"
                />
              </div>
              <div class="col-span-2 bg-gray-300 border-l border-gray-400">
                <div class="flex flex-col justify-center items-center h-full">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-gray-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                  </svg>
                </div>
              </div>
            </div>
          </header>
          <!-- Body -->
          <div class="p-3 lg:p-0 bg-white">
            <div class="grid gap-4 sm:grid-cols-2 lg:hidden">
              <user-card
                v-for="user in users"
                :key="user.id"
                :user="user"
              ></user-card>
            </div>

            <user-table :users="users"></user-table>
          </div>

          <!-- Footer -->
          <footer class="sticky bottom-0 flex justify-between p-2 sm:p-4 bg-blue-200 border-t border-blue-300">
            <div class="text-sm text-gray-600">
              Usuarios: <span>{{ users.length }}</span>
            </div>
            <div class="text-sm text-gray-600">
              <span class="lg:hidden">Verificados:</span>
              <span class="hidden lg:inline-block">Usuarios Verificados:</span>
              <span class="font-bold">{{ usersVerified }}</span>
            </div>
            <div class="text-sm text-gray-600">
              <span class="lg:hidden">Activos:</span>
              <span class="hidden lg:inline-block">Usuarios Activos:</span>
              <span>{{ usersActive }}</span>
            </div>
          </footer>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import UserCard from "./UserCard.vue";
import UserTable from "./Table.vue"
import dayjs from "dayjs";

export default {
  props: ["users"],

  components: {
    AppLayout,
    UserCard,
    JetButton,
    JetDangerButton,
    UserTable
  },
  computed: {
    usersActive(){
      let userActives = this.users.filter(user => {
        if(user.sessions.length > 0){
          let lastActivity = dayjs(user.sessions[0].lastActivity * 1000);
          let now = dayjs();
          let diff = now.diff(lastActivity, 'hours');

          if(diff <= 48){
            return true;
          }

          return false;

        }

        return false;
      });
      return userActives.length;
    },
    usersVerified(){
      return this.users.filter(user => user.email_verified_at).length;
    }
  },
};
</script>