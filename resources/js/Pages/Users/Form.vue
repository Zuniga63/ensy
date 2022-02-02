<template>
  <form @submit.prevent="submit">
    <header class="px-6 py-4 border border-slate-700 rounded-t-md bg-zinc-800">
      <h3 class="text-left text-white font-bold tracking-wider uppercase">
        Nuevo Usuario
      </h3>
    </header>

    <!-- Body -->
    <div
      class="
        px-4
        py-6
        border-x border-slate-700 border-opacity-60
        bg-white bg-opacity-40
      "
    >
      <!-- Nombre de usuario -->
      <div class="mb-2">
        <jet-label for="newUserName" value="Nombre" />
        <jet-input
          id="newUserName"
          name="newUserName"
          type="text"
          class="mt-1 block w-full"
          placeholder="Escribe el nombre del usuario"
          autocomplete="off"
          v-model="form.name"
        />
        <jet-input-error :message="form.errors.name" class="mt-2" />
      </div>

      <!-- Email -->
      <div class="mb-2">
        <jet-label for="newUserEmail" value="Email" />
        <jet-input
          id="newUserEmail"
          name="newUserEmail"
          type="email"
          class="mt-1 block w-full"
          placeholder="Escribe el correo electronico"
          autocomplete="off"
          v-model="form.email"
        />
        <jet-input-error :message="form.errors.email" class="mt-2" />
      </div>

      <!-- Password -->
      <div class="mb-2">
        <jet-label for="newUserPassword" value="Contraseña" />
        <jet-input
          id="newUserPassword"
          name="newUserPassword"
          type="password"
          class="mt-1 block w-full"
          placeholder="Escribe una contraseña"
          autocomplete="off"
          v-model="form.password"
        />
        <jet-input-error :message="form.errors.password" class="mt-2" />
      </div>

      <!-- Confirm Password -->
      <div class="mb-2">
        <jet-label for="newUserConfirmPassword" value="Confirmar Contraseña" />
        <jet-input
          id="newUserConfirmPassword"
          name="newUserConfirmPassword"
          type="password"
          class="mt-1 block w-full"
          placeholder="Confirmars la contraseña"
          autocomplete="off"
          v-model="form.password_confirmation"
        />
        <jet-input-error
          :message="form.errors.password_confirmation"
          class="mt-2"
        />
      </div>
    </div>

    <footer
      class="
        flex
        justify-end
        items-center
        px-6
        py-4
        border border-slate-700
        rounded-b-md
        bg-zinc-800
      "
    >
      <a
        href="javascript:;"
        class="inline-block mr-2 text-white hover:text-opacity-70"
        :class="{
          'text-opacity-20': processing
        }"
        @click="reset"
        >Cancelar</a
      >
      <JetButton class="border border-white">
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
        <span v-show="processing" class="animate-pulse">Procesando...</span>
        <span v-show="!processing">Registrar</span>
      </JetButton>
    </footer>
  </form>
</template>
<script>
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Swal from "sweetalert2";
export default {
  components: {
    JetButton,
    JetDangerButton,
    JetInput,
    JetInputError,
    JetLabel,
    JetActionMessage,
  },
  setup(props) {
    const form = useForm({
      name: null,
      email: null,
      password: null,
      password_confirmation: null,
    });

    return { form };
  },
  data() {
    return {
      processing: false,
    };
  },
  methods: {
    reset(){
      if(!this.processing){
        this.form.reset();
      }
    },
    /**
     * Se encarga d realizar la peticón al servidor para 
     * guardar los datos del nuevo usuario.
     */
    submit() {
      let url = route('users.store');
      console.log(`Url: ${url}`);
      this.form.post(url, {
        preserveState: true,
        preserveScroll: true,
        onStart:()=>{
          this.processing = true;
        },
        onSuccess:page => {
          let data = page.props.flash.message;
          let userName = `<span class="text-bold">${data.userName}</span>`;
          let userEmail = `<span class="text-bold">${data.email}</span>`;
          let htmlText = `El usuario ${userName} con correo ${userEmail}`;

          Swal.fire({
            title: 'Usuario Registrado',
            icon: 'success',
            html: htmlText
          })

          this.form.reset();
        },
        onError: (errors) => {
          Swal.fire({
            title: '¡Opps, Algo salio mal!',
            icon: 'error',
            text: 'Revisa si todos los campos han sido correctamente diligenciados',
          });
        },
        onFinish: visit => {
          this.processing = false;
          console.log(visit);
        }
      })
    },
  },
};
</script>