<template>
  <app-layout title="Usuarios">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Administración de Usuarios
      </h2>
    </template>

    <div>
      <!-- Contenedor Principal -->
      <div
        class="
          block
          lg:grid lg:grid-cols-12 lg:gap-4
          max-w-7xl
          pt-5
          lg:pb-16
          sm:px-6
          lg:px-8 lg:mt-3
          mx-auto
        "
      >
        <!-- Formulario de nuevo usuario -->
        <div class="hidden lg:block col-span-4">
          <new-user-form />
        </div>
        <!-- Tarjetas de usuarios -->
        <div
          class="
            relative
            lg:col-span-8
            max-h-screen
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
              border border-slate-700
              sm:rounded-t
              lg:rounded-t-md
              bg-zinc-800
            "
          >
            <div
              class="
                hidden
                sm:flex
                items-center
                lg:col-span-2
                text-white text-lg
              "
            >
              <p class="hidden sm:block lg:col-span-2 text-white text-lg">
                Listado de Usuarios
              </p>
              <a
                href="javascript:;"
                class="
                  inline-block
                  ml-2
                  text-white text-opacity-50
                  hover:text-opacity-100
                "
                @click="reload"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
              </a>
            </div>
            <!-- Buscador -->
            <input
              type="text"
              class="
                block
                w-full
                px-4
                py-2
                border border-white
                rounded
                focus:ring
                focus:border-slate-700
                focus:ring-slate-400
                focus:ring-opacity-80
                focus:outline-none
              "
              placeholder="Buscar usuario"
            />
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

            <user-table :users="users" @destroy-user="confirmDelete"></user-table>
          </div>

          <!-- Footer -->
          <footer
            class="
              sticky
              bottom-0
              flex
              justify-between
              p-2
              sm:p-4
              border border-slate-700
              sm:rounded-b
              bg-zinc-800
            "
          >
            <div class="text-sm text-gray-50">
              Usuarios: <span>{{ users.length }}</span>
            </div>
            <div class="text-sm text-gray-50">
              <span class="lg:hidden">Verificados:</span>
              <span class="hidden lg:inline-block">Usuarios Verificados:</span>
              <span class="font-bold">{{ usersVerified }}</span>
            </div>
            <div class="text-sm text-gray-50">
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
import UserCard from "./UserCard.vue";
import UserTable from "./Table.vue";
import NewUserForm from "./Form.vue";
import dayjs from "dayjs";
import { Inertia } from "@inertiajs/inertia";
import Swal from "sweetalert2";

export default {
  props: ["users"],

  components: {
    AppLayout,
    UserCard,
    UserTable,
    NewUserForm,
  },
  methods: {
    reload() {
      Inertia.reload({ only: ["users"] });
    },
    confirmDelete(user) {
      Swal.fire({
        title: "¿Está seguro?",
        text: "Está acción no puede revertirse y eliminará todos los datos del usuario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Si, Eliminalo!",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          this.destroyUser(user);
        }
      });
    },
    destroyUser(user) {
      Inertia.delete(route("users.destroy", user.id), {
        preserveScroll: true,
        onSuccess: (page) => {
          let message = page.props.flash.message;
          if (message) {
            Swal.fire({
              title: "Opps, Algo salio mal",
              icon: "error",
              text: message,
            });
          } else {
            Swal.fire({
              title: "Usuario Eliminado.",
              icon: "success",
            });
          }
        },
      });
    },
  },
  computed: {
    usersActive() {
      let userActives = this.users.filter((user) => {
        if (user.sessions.length > 0) {
          let lastActivity = dayjs(user.sessions[0].lastActivity * 1000);
          let now = dayjs();
          let diff = now.diff(lastActivity, "hours");

          if (diff <= 48) {
            return true;
          }

          return false;
        }

        return false;
      });
      return userActives.length;
    },
    usersVerified() {
      return this.users.filter((user) => user.email_verified_at).length;
    },
  },
};
</script>