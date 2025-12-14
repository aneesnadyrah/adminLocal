"use strict";
var colors = [
  config.colors.primary,
  config.colors.warning,
  config.colors.success,
  config.colors.danger,
  config.colors.info,
  config.colors.secondary,
  config.colors.dark
];

var pieChart = (function () {
  var initPieChart = () => {
    function renderPieChart(chartId, series, type, labels) {
      var options = {
        chart: {
          type: type,
          height: 350,
          offsetY: 30,
        },
        series: series,
        labels: labels,
        colors: colors,
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                name: {
                  show: true,
                  fontSize: "24px",
                  color: config.colors["text-dark"],
                },
                value: {
                  show: true,
                  fontSize: "22px",
                  fontWeight: 700,
                  color: config.colors.danger,
                  formatter: function (val) {
                    return val;
                  },
                },
                total: {
                  show: true,
                  label: "Jumlah",
                  color: config.colors["text-dark"],
                  fontWeight: 800,
                  formatter: function (w) {
                    return w.globals.seriesTotals.reduce((a, b) => {
                      return a + b;
                    }, 0);
                  },
                },
              },
            },
          },
        },
        legend: {
          position: "bottom",
          fontWeight: 600,
          formatter: function (val, opts) {
            return val + " - " + opts.w.globals.series[opts.seriesIndex];
          },
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                width: 200,
              },
            },
          },
        ],
      };

      var pieChart = new ApexCharts(
        document.querySelector(`[data-pie-chart="${chartId}"]`),
        options
      );
      pieChart.render();
    }

    var elements = [].slice.call(document.querySelectorAll("[data-pie-chart]"));
    elements.forEach(function (element) {
      var chartId = element.getAttribute("data-pie-chart");
      var series = JSON.parse(element.getAttribute("data-pie-series"));
      var type = element.getAttribute("data-pie-type");
      var labels = JSON.parse(element.getAttribute("data-pie-labels"));

      renderPieChart(chartId, series, type, labels);
    });
  };

  return {
    init: function () {
      initPieChart();
    },
  };
})();

var lineChart = (function () {
  var initLineChart = () => {
    function renderLineChart(chartId, name, data, labels, style) {
      const series = name.map((name, index) => {
        return {
          name: name,
          data: data[index],
        };
      });
      var options = {
        series: series,
        chart: {
            height: 350,
            type: "area",
            toolbar: {
                show: false,
                tools: {
                    download: false,
                    selection: false,
                    zoom: false,
                    zoomin: true,
                    zoomout: true,
                    pan: false,
                    reset: false,
                },
                autoSelected: 'selection'
            },
            offsetY: -40,
        },
        colors: colors,
        tooltip: {
          enabled: true,
          y: {
            formatter: function (value) {
              return value.toLocaleString();
            },
            title: {
              formatter: (seriesName) => seriesName,
            },
          },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: style,
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          categories: labels,
          labels: {
            style: {
                fontWeight: 600,
            },
          }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          labels: {
            show: false
          }
        },
      };

      var lineChart = new ApexCharts(document.querySelector(`[data-line-chart="${chartId}"]`), options);
      lineChart.render();
    }

    var elements = [].slice.call(
      document.querySelectorAll("[data-line-chart]")
    );
    elements.forEach(function (element) {
      var chartId = element.getAttribute("data-line-chart");
      var style = element.getAttribute("data-line-style");
      var name = JSON.parse(element.getAttribute("data-line-name"));
      var series = JSON.parse(element.getAttribute("data-line-series"));
      var labels = JSON.parse(element.getAttribute("data-line-labels"));

      renderLineChart(chartId, name, series, labels, style);
    });
  };

  return {
    init: function () {
      initLineChart();
    },
  };
})();

var barChart = (function () {
  var initBarChart = () => {
    function renderbarChart(chartId, name, data, labels, type, height = 350, style = "column") {
      const series = name.map((name, index) => {
        return {
          name: name,
          data: data[index],
        };
      });

      var options = {
        series: series,
        chart: {
          type: 'bar',
          height: height,
          stacked: type === 'stacked' ? true:false,
          stackType: type === 'stacked' ? '100%':'',
          toolbar: {
            show: false
          },
          offsetY: -15,
        },
        grid: {
          show: false,
        },
        colors: colors,
        plotOptions: {
          bar: {
            horizontal: style === 'column' ? false : true,
          },
        },
        tooltip: {
          enabled: true,
          y: {
            formatter: function (value) {
              return value.toLocaleString();
            },
            title: {
              formatter: (seriesName) => seriesName,
            },
          },
        },
        xaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          categories: labels,
          labels: {
            style: {
                fontWeight: 600,
            },
          }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          labels: {
            show: false
          }
        },

        fill: {
          opacity: 1
        },
        
        legend: {
          position: 'bottom',
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
              position: 'bottom',
              offsetX: -10,
              offsetY: 0
            }
          }
        }],
      };

      var barChart = new ApexCharts (document.querySelector(`[data-bar-chart="${chartId}"]`),options);
      barChart.render();
    
    }

    var elements = [].slice.call(
      document.querySelectorAll("[data-bar-chart]")
    );
    elements.forEach(function (element) {
      var chartId = element.getAttribute("data-bar-chart");
      var type = element.getAttribute("data-bar-type");
      var style = element.getAttribute("data-bar-style");
      var height = parseInt(element.getAttribute("data-bar-height"));
      var name = JSON.parse(element.getAttribute("data-bar-name"));
      var series = JSON.parse(element.getAttribute("data-bar-series"));
      var labels = JSON.parse(element.getAttribute("data-bar-labels"));

      renderbarChart(chartId, name, series, labels, type, height, style);
    });
  };

  return {
    init: function () {
      initBarChart();
    },
  };
})();

var radialChart = (function () {
  var initRadialChart = () => {
    function renderRadialChart(chartId, series, labels, style, height = 350) {
      var options;
      switch (style) {
        case 'radial':
          options = {
            series: series,
            chart: {
              height: height,
              type: 'radialBar',
            },
            colors: colors,
            plotOptions: {
              radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: {
                  margin: 20,
                  size: '40%',
                  background: 'transparent',
                },
                dataLabels: {
                  name: {
                    show: false,
                  },
                  value: {
                    show: true,
                  }
                }
              }
            },
            stroke: {
              lineCap: 'round'
            },
            labels: labels,
            legend: {
              show: true,
              floating: true,
              fontSize: '16px',
              fontWeight: 600,
              position: 'left',
              offsetY: 25,
              labels: {
                useSeriesColors: true,
              },
              formatter: function(seriesName, opts) {
                return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex] + " %";
              },
              itemMargin: {
                vertical: 3
              }
            },
            responsive: [{
              breakpoint: 480,
              options: {
                legend: {
                    show: false
                }
              }
            }]
          };
        break;
        case 'gauge':
          options = {
            series: series,
            chart: {
              height: 350,
              type: 'radialBar',
              offsetY: -10
            },
            plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                  name: {
                    fontSize: '16px',
                    offsetY: 120
                  },
                  value: {
                    offsetY: 76,
                    fontSize: '22px',
                    formatter: function (val) {
                      return val + "%";
                    }
                  }
                }
              }
            },
            fill: {
              type: 'gradient',
              gradient: {
                shade: 'dark',
                shadeIntensity: 0.15,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 65, 91]
              },
            },
            stroke: {
              dashArray: 4
            },
            labels: labels,
          };
        break;
      }

      var radialChart = new ApexCharts(
        document.querySelector(`[data-radial-chart="${chartId}"]`),
        options
      );
      radialChart.render();
    }

    var elements = [].slice.call(document.querySelectorAll("[data-radial-chart]"));
    elements.forEach(function (element) {
      var chartId = element.getAttribute("data-radial-chart");
      var style = element.getAttribute("data-radial-style");
      var height = parseInt(element.getAttribute("data-radial-height"));
      var series = JSON.parse(element.getAttribute("data-radial-series"));
      var labels = JSON.parse(element.getAttribute("data-radial-labels"));

      renderRadialChart(chartId, series, labels, style, height);
    });
  };

  return {
    init: function () {
      initRadialChart();
    },
  };
})();

KTUtil.onDOMContentLoaded(function () {
  pieChart.init();
  lineChart.init();
  barChart.init();
  radialChart.init();
});
