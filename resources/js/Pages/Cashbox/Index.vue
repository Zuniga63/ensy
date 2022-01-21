<template>
  <app-layout title="Cajas">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Administración de Cajas
      </h2>

      <p class="text-sm lg:text-lg text-gray-400">
        En esta sección se encuentran listadas todas las cajas registradas en la
        plataforma.
      </p>
    </template>

    <div class="pt-5">
      <!-- Vista mobile -->
      <div class="lg:hidden w-full min-h-screen pb-32">
        <!-- Contendor de cajas -->
        <div class="w-11/12 mx-auto">
          <Box
            v-for="box in boxs"
            :key="box.id"
            :box="box"
            @delete-box="confirmDeleteBox"
          />
          <!-- /.end cashbox -->
        </div>
        <!-- /.end box container -->

        <div class="fixed bottom-0 w-full">
          <div class="px-6 py-3 bg-gray-800 text-gray-100">
            <h2 class="text-center uppercase">Capital en Efectivo</h2>
            <p class="mb-2 text-center text-lg tracking-widest">{{ cash }}</p>
            <Link
              :href="route('cashbox.create')"
              class="
                block
                w-full
                px-4
                py-2
                border border-white
                rounded-md
                bg-transparent
                hover:bg-gray-700
                text-white text-sm text-center
                tracking-wider
                uppercase
                font-bold
              "
            >
              Crear Caja
            </Link>
          </div>
        </div>
      </div>

      <!-- Vista Escritorio -->
      <div class="hidden lg:block | max-w-7xl py-10 px-8 mx-auto shadow">
        <BoxTable :boxs="boxs" @delete-box="confirmDeleteBox" />
      </div>
    </div>
  </app-layout>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import Swal from "sweetalert2";

import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import Box from "@/Pages/Cashbox/Box.vue";
import BoxTable from "@/Pages/Cashbox/Table.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";

export default {
  components: {
    AppLayout,
    JetButton,
    JetDangerButton,
    Link,
    Box,
    BoxTable,
  },
  props: ["boxs"],
  setup(props) {
    //---------------------------------------------------------
    // SE CONSTRUYE EL FORMATEADOR DE MONEDA
    //---------------------------------------------------------
    let fractionDigits = 0;
    let style = "currency";
    let currency = "COP";

    let formater = new Intl.NumberFormat("es-CO", {
      style,
      currency,
      minimumFractionDigits: fractionDigits,
    });

    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);

    //--------------------------------------------------
    //  SE AGREGA LA FECHA RELATIVA DEL ULTIMO CIERRE
    //--------------------------------------------------
    props.boxs.map((item) => {
      item.lastClosureFromNow = item.lastClosure
        ? dayjs(item.lastClosure).fromNow()
        : null;
    });

    return { formater };
  },
  methods: {
    formatCurrency(number) {
      return this.formater.format(number);
    },
    deleteBox(boxId) {
      let url = route("cashbox.destroy", boxId);
      Inertia.delete(url, {
        preserveScroll: true,
        onSuccess: (page) => {
          let result = page.props.flash?.message;
          let icon = "success";
          let title = "¡Caja Eliminada!";
          let message = result?.message;

          if(result){
            if(!result.ok){
              icon = 'warning';
              title = "¡No se puede eliminar!";
            }
          }else{
            icon = 'error';
            title = '¡Oops';
            message = "Algo salio mal, comuniquese con el adminsitrador.";
          }

          Swal.fire({
            icon,
            title,
            html: message,
          });
        },
      });
    },
    confirmDeleteBox(boxId) {
      Swal.fire({
        title: "¿Está seguro?",
        text: "Está acción no puede revertirse y eleminará la instancia de la caja mas no las transacciones. Estás ultimas pasan a ser visibles solo en el ambito general.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Si, Eliminalo!",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteBox(boxId);
        }
      });
    },
  },
  mounted() {
    /**
     * Cada minuto se actualiza el tiempo relativo de la fecha de corte
     */
    setInterval(() => {
      this.boxs.map((item) => {
        item.lastClosureFromNow = item.lastClosure
          ? dayjs(item.lastClosure).fromNow()
          : null;
      });
    }, 60000);
  },
  computed: {
    cash() {
      let amount = 0;
      this.boxs.forEach((box) => {
        amount += box.balance;
      });

      return this.formatCurrency(amount);
    },
  },
};
</script>