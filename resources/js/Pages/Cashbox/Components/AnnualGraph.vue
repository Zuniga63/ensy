<template>
  <div class="border border-slate-900 rounded-md overflow-hidden">
    <header class="p-2 bg-slate-900 text-white">
      <h2 class="block text-center font-bold text-xl uppercase"> Flujo de caja del {{ title }} </h2>
    </header>
    <div class="px-4 py-6">
      <canvas ref="mainChart"></canvas>
    </div>
  </div>
</template>

<script>
import Chart from "chart.js/auto";
import { formatCurrency } from "@/utilities";
import { CHART_COLORS, transparentize } from "@/chartUtils";

export default {
  props: {
    reports: Array,
    title: String,
  },
  data() {
    return {
      mainChart: null,
    };
  },
  methods: {
    /**
     * @param {String} label
     * @param {String} color
     * @param {Array} data
     * @param {Boolean} hidden
     */
    createDataset(label, color, data, hidden = false) {
      return {
        label,
        hidden,
        borderColor: color,
        backgroundColor: transparentize(color, 0.4),
        data,
      };
    },
    buildChart() {
      if (this.mainChart) {
        this.mainChart.destroy();
      }

      this.mainChart = new Chart(this.$refs.mainChart, this.charConfig);
    },
  },
  computed: {
    /**
     * Recupera los nombre de los meses a pintar en la grafica.
     * @return {Array}
     */
    labels() {
      const labels = [];
      this.reports.forEach((report) => labels.push(report.label.toString()));
      return labels;
    },
    datasets() {
      const balanceData = [];
      const incomesData = [];
      const expensesData = [];

      const datasets = [];

      this.reports.forEach((report) => {
        balanceData.push(report.balance);
        incomesData.push(report.incomes);
        expensesData.push(report.expenses);
      });

      datasets.push(this.createDataset("Ingresos", CHART_COLORS.green, incomesData));
      datasets.push(this.createDataset("Egresos", CHART_COLORS.red, expensesData));
      datasets.push(this.createDataset("Saldo", CHART_COLORS.blue, balanceData, true));

      return datasets;
    },
    charConfig() {
      const type = "bar";
      const data = { labels: this.labels, datasets: this.datasets};
      const options = this.chartOptions;

      return { type, data, options };
    },
    chartOptions() {
      return {
        responsive: true,
        plugins: {
          legend: {
            position: "top",
          },
          /* title: {
            display: true,
            text: "Reporte Anual",
          }, */
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
      };
    },
  }, //.end computed
  mounted() {
    this.buildChart();
  },
  updated() {
    this.buildChart();
  },
};
</script>