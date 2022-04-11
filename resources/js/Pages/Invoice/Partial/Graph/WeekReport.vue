<template>
  <div class="grid grid-cols-3 gap-4 px-4 py-4">
    <!-- Reprte de ventas y recaudo -->
    <div class="col-span-3 p-4 rounded bg-white bg-opacity-10 backdrop-blur-sm shadow">
      <canvas ref="mainChart"></canvas>
    </div>
  </div>
</template>

<script>
import dayjs from "dayjs";
import locale_es_do from "dayjs/locale/es-do";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import isSameOrBefore from "dayjs/plugin/isSameOrBefore";
import { formatCurrency } from "@/utilities";
import Chart from "chart.js/auto";

/**
 * Objeto para crear reportes diarios de ventas.
 */
class DayReport {
  /**
   * @param {string} day - Nombre del día en el que se hace el reporte
   */
  constructor(day) {
    this.day = day;
    this.sales = 0;
    this.credits = 0;
    this.cash = 0;
    this.collect = 0;
    this.invoices = [];
    this.payments = [];
  }

  /**
   * Agregar una nueva factura de venta al reporte y actualiza
   * el estado del reporte.
   * @param {object} invoice - factura de venta.
   */
  addInvoice(invoice) {
    this.invoices.push(invoice);
    this.sales += parseFloat(invoice.amount);
    this.credits += invoice.credit ? parseFloat(invoice.credit) : 0;
    this.cash += invoice.cash ? parseFloat(invoice.cash) : 0;
    this.cash -= invoice.change ? parseFloat(invoice.change) : 0;
  }

  /**
   * Agrega un pago al listado y actualiza el valor del recaudo.
   * @param {object} payment - intancia del pago.
   */
  addPayments(payment) {
    this.payments.push(payment);
    this.collect += parseFloat(payment.amount);
  }
}

export default {
  props: {
    report: Object,
  },
  setup(props) {
    dayjs.locale(locale_es_do);
    dayjs.extend(isSameOrAfter);
    dayjs.extend(isSameOrBefore);
  },
  data() {
    return {
      startWeek: dayjs().subtract(6, "days").startOf("days"),
      endWeek: dayjs(),
      mainChart: null,
    };
  },
  methods: {
    formatCurrency,
    buildWeeklyChart() {
      let labels = [];
      let sales = [];
      let cash = [];
      let credits = [];
      let collect = [];

      //Se obtiene la informacion de los sets
      this.weeklyReport.forEach((report) => {
        labels.push(report.day);
        sales.push(report.sales);
        cash.push(report.cash);
        credits.push(report.credits);
        collect.push(report.collect);
      }); //.end forEach

      //Se define que etiquetas se muestran
      let datasets = [];
      //Se construye el set de ventas
      if (sales.reduce((carry, current) => carry + current) > 0) {
        datasets.push({
          label: "Ventas",
          data: sales,
          borderColor: "rgb(75, 192, 192)",
          backgroundColor: "rgba(75, 192, 192, 0.5)",
        });
      }

      //Se construye el set de creditos
      if (credits.reduce((carry, current) => carry + current) > 0) {
        datasets.push({
          label: "Creditos",
          hidden: true,
          data: credits,
          borderColor: "rgb(54, 162, 235)",
          backgroundColor: "rgba(54, 162, 235, 0.5)",
        });
      }

      //Se construye el set de efectivo
      if (cash.reduce((carry, current) => carry + current) > 0) {
        datasets.push({
          label: "Efectivo",
          data: cash,
          hidden: true,
          borderColor: "rgb(255, 99, 132)",
          backgroundColor: "rgba(255, 99, 132, 0.5)",
        });
      }

      //Se constuye el set de abonos
      if (collect.reduce((carry, current) => carry + current) > 0) {
        datasets.push({
          label: "Depositos",
          data: collect,
          hidden: true,
          borderColor: "rgb(255, 159, 64)",
          backgroundColor: "rgba(255, 159, 64, 0.5)",
        });
      }

      const config = {
        type: "bar",
        data: {
          labels,
          datasets,
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "top",
            },
            title: {
              display: true,
              text: "Reporte Semanal",
            },
            tooltip: {
              callbacks: {
                label: (context) => {
                  let label = context.dataset.label || "";

                  if (label) {
                    label += ": ";
                  }

                  if (context.parsed.y !== null) {
                    label += formatCurrency(context.parsed.y, 0);
                  }

                  return label;
                },
              },
            },
          },
          scales: {
            y: {
              ticks: {
                beginAtZero: true,
                callback: function (value, index, values) {
                  return formatCurrency(value);
                }, //end callback
              }, //end ticks
            },
          }, //end scales
        },
      };

      if (this.mainChart) {
        this.mainChart.destroy();
      }

      this.mainChart = new Chart(this.$refs.mainChart, config);
      //Se obtienen los labels
    },
  },
  computed: {
    weeklyReport() {
      const weekReport = [];
      let date = this.startWeek.clone();
      let invoiceIndex = 0; //Para guardar donde debe empezar a recorrer el lsitado
      let paymentIndex = 0;
      let count = 0; //Count que debe estar entre 0 y 7

      while (date.isBefore(this.endWeek)) {
        //Se recupera el nombre del día y se capitaliza.
        let day = date.format("dddd").split("");
        day[0] = day[0].toUpperCase();
        day = day.join("");

        //Se establece el inicio y el final del día
        let startDay = date.clone().startOf("day");
        let endDay = date.clone().endOf("day");

        //Se crea el reporte diario
        const dayReport = new DayReport(day);

        //Se recorren las facturas para irlas agregando al reporte
        for (let index = invoiceIndex; index < this.report.invoices.length; index++) {
          //Se recupera la factura
          const invoice = this.report.invoices[index];
          //Se crea la intancia de la fecha
          const invoiceDate = dayjs(invoice.date);

          //Se agrega al reporte si está entre las fechas
          if (invoiceDate.isSameOrAfter(startDay) && invoiceDate.isSameOrBefore(endDay)) {
            dayReport.addInvoice(invoice);
            invoiceIndex++;
          } else {
            //*Se rompe el bucle si es mayor, sin actualizar el index global
            break;
          }
        }

        //Se agregan los recuados
        for (let index = paymentIndex; index < this.report.payments.length; index++) {
          const payment = this.report.payments[index];
          const paymentDate = dayjs(payment.date);

          if (paymentDate.isSameOrAfter(startDay) && paymentDate.isSameOrBefore(endDay)) {
            dayReport.addPayments(payment);
            paymentIndex++;
          } else {
            break;
          }
        }

        //Se agrega el reporte
        weekReport.push(dayReport);
        //Se actualiza la fecha
        date = date.add(1, "day");

        //Seguro contra bucle infinito
        count++;
        if (count > 7) break;
      } //end while

      return weekReport;
    },
  },
  mounted() {
    this.buildWeeklyChart();
  },
  watch: {
    report() {
      this.buildWeeklyChart();
    },
  },
};
</script>
