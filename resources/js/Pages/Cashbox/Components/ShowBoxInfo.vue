<template>
  <div class="grid grid-cols-6">
    <div class="col-span-6 lg:col-span-2 px-4">
      <canvas ref="generalChart"></canvas>
    </div>
    <div class="col-span-6 lg:col-span-4">
      <canvas ref="weekReport"></canvas>
    </div>
  </div>
</template>

<script>
import Chart from "chart.js/auto";
import dayjs from "dayjs";
import locale_es_do from "dayjs/locale/es";

export default {
  props: {
    transactions: {
      type: Array,
      default: [],
    },
  },
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

    window.formater = formater;

    //----------------------------------------------------
    // SE ESTABLECEN LOS PARAMETROS DE dayjs
    //----------------------------------------------------
    dayjs.locale(locale_es_do);

    return { formater };
  },
  data() {
    return {
      generalChart: null,
      weekReport: null,
    };
  },
  methods: {
    formatCurrency(number) {
      return this.formater.format(number);
    },
  },
  computed: {
    generalDataset() {
      let info = {
        incomes: 0,
        expenses: 0,
        deposits: 0,
        transfers: 0,
      };

      this.transactions.forEach((item) => {
        let isIncome = item.amount >= 0 ? true : false;
        let isTransfer = item.transfer;
        let amount = Math.abs(item.amount);

        if (isIncome) {
          isTransfer ? (info.deposits += amount) : (info.incomes += amount);
        } else {
          isTransfer ? (info.transfers += amount) : (info.expenses += amount);
        }
      });

      let data = {
        labels: ["Ingresos", "Depositos", "Egresos", "Transferencias"],
        datasets: [
          {
            label: "Movimientos Generales",
            data: [info.incomes, info.deposits, info.expenses, info.transfers],
            backgroundColor: [
              "rgb(75, 192, 192)",
              "rgb(54, 162, 235)",
              "rgb(255, 99, 132)",
              "rgb(255, 159, 64)",
            ],
          },
        ],
      };

      return {
        type: "doughnut",
        data: data,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "top",
            },
            title: {
              display: true,
              text: "Movimientos Generales",
            },
            tooltip: {
              callbacks: {
                label: (context) => {
                  let label = context.label || "";

                  if (label) {
                    label += ": ";
                  }

                  if (context.parsed.y !== null) {
                    label += this.formatCurrency(context.parsed, 0);
                  }

                  return label;
                },
              },
            },
          },
        },
      };
    },
    weeklyReportDataset() {
      let today = dayjs();
      let date = today.subtract(7, "day").startOf("day");
      let labels = [];
      let incomes = [];
      let expenses = [];

      today.isAfter();
      let lastWeekTransaction = this.transactions.filter((item) => {
        return (
          item.date.isAfter(date) &&
          (item.date.isBefore(today) || item.date.isSame(today)) &&
          !item.transfer
        );
      });

      while (date.isBefore(today)) {
        //Se guarda el nombre del día
        labels.push(date.format("dddd"));
        let startDay = date.clone().startOf('daye');
        let endDay = date.endOf("day");
        let dailyTransactions = lastWeekTransaction.filter((trans) => {
          return (
            trans.date.isAfter(startDay) &&
            (trans.date.isBefore(endDay) || trans.date.isSame(endDay))
          );
        });

        if (dailyTransactions.length) {
          //Se recuperan los ingresos y egresos
          let incomeAmounts = dailyTransactions.map((t) =>
            t.amount >= 0 ? t.amount : 0
          );
          let expenseAmounts = dailyTransactions.map((t) =>
            t.amount < 0 ? t.amount : 0
          );
          incomes.push(
            incomeAmounts.length
              ? incomeAmounts.reduce((prev, next) => prev + next)
              : 0
          );
          expenses.push(
            expenseAmounts.length
              ? Math.abs(expenseAmounts.reduce((prev, next) => prev + next))
              : 0
          );
        }else{
          incomes.push(0);
          expenses.push(0);
        }

        //Se incrementa un día
        date = date.add(1, "day");
      }

      let data = {
        labels: labels,
        datasets: [
          {
            label: "Ingresos",
            data: incomes,
            borderColor: "rgb(75, 192, 192)",
            backgroundColor: "rgba(75, 192, 192, 0.5)",
          },
          {
            label: "Egresos",
            data: expenses,
            borderColor: "rgb(255, 99, 132)",
            backgroundColor: "rgba(255, 99, 132, 0.5)",
          },
        ],
      };

      return {
        type: "bar",
        data: data,
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
                    label += this.formatCurrency(context.parsed.y, 0);
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
                  return window.formater.format(value);
                }, //end callback
              }, //end ticks
            },
          }, //end scales
        },
      };
    },
  },
  mounted() {
    //Se monta la grafica generañ
    //console.log(this.$refs.generalChart);
    this.generalChart = new Chart(this.$refs.generalChart, this.generalDataset);
    this.weekReport = new Chart(
      this.$refs.weekReport,
      this.weeklyReportDataset
    );
  },
  watch: {
    generalDataset() {
      this.generalChart.destroy();
      this.generalChart = new Chart(
        this.$refs.generalChart,
        this.generalDataset
      );
    },
    weeklyReportDataset() {
      this.weekReport.destroy();
      this.weekReport = new Chart(
        this.$refs.weekReport,
        this.weeklyReportDataset
      );
    },
  },
};
</script>