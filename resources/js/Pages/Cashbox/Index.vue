<template>
  <app-layout title="Cajas">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administración de Cajas</h2>

      <p class="text-sm lg:text-lg text-gray-400">
        En esta sección se encuentran listadas todas las cajas registradas en la plataforma.
      </p>
    </template>

    <div class="w-full lg:w-11/12 lg:mx-auto pt-5 px-2 lg:px-0">
      <tab-component :tabs="tabs" :tabSelected="tab" @selectTab="tab = $event">
        <template #controls>
          <link-new-box />
        </template>

        <template #cajas>
          <!-- Vista mobile -->
          <div class="lg:hidden w-full min-h-screen pb-32 pt-4">
            <!-- Contendor de cajas -->
            <Box v-for="box in boxs" :key="box.id" :box="box" @delete-box="confirmDeleteBox" />
          </div>

          <!-- Vista Escritorio -->
          <div class="hidden lg:block py-5 mx-auto shadow">
            <BoxTable :boxs="boxs" @delete-box="confirmDeleteBox" />
          </div>
        </template>

        <template #graficas>
          <div class="grid grid-cols-12 gap-8">
            <annual-graph
              :reports="annualReport[0].reports"
              :title="annualReport[0].year"
              class="col-span-12 2xl:col-span-6"
            />
            <annual-graph
              :reports="annualReport[1].reports"
              :title="annualReport[1].year"
              class="col-span-12 2xl:col-span-6"
            />
          </div>
        </template>
      </tab-component>

      <div class="fixed bottom-0 w-full lg:hidden">
        <div class="px-6 py-3 bg-gray-800 text-gray-100">
          <h2 class="text-center uppercase">Capital en Efectivo</h2>
          <p class="text-center text-lg tracking-widest">{{ cash }}</p>
        </div>
      </div>
    </div>
  </app-layout>
</template>
<script>
import { Inertia } from "@inertiajs/inertia";
import Swal from "sweetalert2";

import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import Box from "@/Pages/Cashbox/Box.vue";
import BoxTable from "@/Pages/Cashbox/Table.vue";
import TabComponent from "@/Components/Tab.vue";
import LinkNewBox from "./Components/LinkNewBox.vue";
import AnnualGraph from "./Components/AnnualGraph.vue";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale_es_do from "dayjs/locale/es";
import { formatCurrency } from "@/utilities";

export default {
  components: {
    AppLayout,
    JetButton,
    JetDangerButton,
    Box,
    BoxTable,
    TabComponent,
    LinkNewBox,
    AnnualGraph,
  },
  props: ["boxs", "annualReport"],
  setup(props) {
    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale(locale_es_do);
    dayjs.extend(relativeTime);

    //--------------------------------------------------
    //  SE AGREGA LA FECHA RELATIVA DEL ULTIMO CIERRE
    //--------------------------------------------------
    props.boxs.map((item) => {
      item.lastClosureFromNow = item.lastClosure ? dayjs(item.lastClosure).fromNow() : null;
    });
  },
  data() {
    return {
      tabs: ["cajas", "graficas"],
      tab: "cajas",
    };
  },
  methods: {
    formatCurrency,
    deleteBox(boxId) {
      let url = route("cashbox.destroy", boxId);
      Inertia.delete(url, {
        preserveScroll: true,
        onSuccess: (page) => {
          let result = page.props.flash?.message;
          let icon = "success";
          let title = "¡Caja Eliminada!";
          let message = result?.message;

          if (result) {
            if (!result.ok) {
              icon = "warning";
              title = "¡No se puede eliminar!";
            }
          } else {
            icon = "error";
            title = "¡Oops";
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
        item.lastClosureFromNow = item.lastClosure ? dayjs(item.lastClosure).fromNow() : null;
      });
    }, 60000);
  },
  computed: {
    cash() {
      let amount = 0;
      this.boxs.forEach((box) => {
        amount += box.balance;
      });

      return formatCurrency(amount);
    },
  },
};
</script>